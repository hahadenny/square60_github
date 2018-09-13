<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\EstateInfo;
use App\User;
use App\Feature;
use App\Premium;
use App\Membership;
use App\Failed;
use App\Building;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Error\Card;

class CheckFeatureListing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckFeatureListing:disableFeature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $domain = env('APP_URL');

        /*$features = DB::table('features')->where('ends_at', '<', $today)->where('renew', '<>', 1)->get();

        foreach($features as $feature){
            $estate = EstateInfo::find($feature->listing_id);
            if ($estate) {
                $estate->feature = 0;
                $estate->save();
            }
        }*/

        $featured_listings = DB::table('features')->whereNotIn('status', ['renew_failed', 'ended', 'renewed'])->distinct()->get(['listing_id']);

        foreach ($featured_listings as $listing) {
            //echo $listing->listing_id.": "; 
            $featured = DB::table('features')->where('listing_id', '=', $listing->listing_id)->where('status', 'success')->orderBy('renew', 'DESC')->orderBy('ends_at', 'DESC')->get()->first();
            //echo $featured->ends_at."\n"; exit;

            if ($featured->renew == 1) { //check if it's time to charge(renew) again
                if (substr($featured->ends_at, 0, 10) <= $today) { //feature ends today, charge again
                    $user = User::find($featured->user_id);

                    if($featured->period == '1w'){
                        $price = env('FEAT_1W');
                        $ends_at = Carbon::now()->addWeek();
                    }elseif ($featured->period == '2w'){
                        $price = env('FEAT_2W');
                        $ends_at = Carbon::now()->addWeeks(2);
                    }elseif ($featured->period == '4w'){
                        $price = env('FEAT_4W');
                        $ends_at = Carbon::now()->addWeeks(4);
                    }elseif ($featured->period == '6w'){
                        $price = env('FEAT_6W');
                        $ends_at = Carbon::now()->addWeeks(6);
                    }elseif ($featured->period == '2m'){
                        $price = env('FEAT_2M');
                        $ends_at = Carbon::now()->addMonths(2);
                    }

                    if ($user->premium) {
                        if ($user->premium == 1)
                            $discount = env('SILV_FEAT_DISC');
                        elseif ($user->premium == 2)
                            $discount = env('GOLD_FEAT_DISC');
                        elseif ($user->premium == 3)
                            $discount = env('DIAM_FEAT_DISC');
            
                        $price = $price * $discount;
                    }

                    $amount = $price * 100;

                    //get default payment method
                    $paym = DB::table('user_payments')->where('user_id', $user->id)->where('in_use', 1)->get()->first();               

                    try {
                        Stripe::setApiKey(env('STRIPE_SECRET'));
                        $response = Charge::create(array(
                            "amount" => $amount,   //e.g. 222 = 2.22
                            "currency" => "usd",
                            "customer" => $paym->cus_id
                        ));           
                    } catch (Card $e) {      
                        $body = $e->getJsonBody();
                        $err  = $body['error'];
                        $err_msg = $err['message'];
                        
                        //log failed payment
                        $failed = new Failed();
                        $failed->user_id = $user->id;
                        $failed->refer_id = $featured->listing_id;
                        $failed->type = 'feature';
                        $failed->renew = 1;
                        $failed->amount = $price;
                        $failed->period = $featured->period;
                        $failed->error = $err_msg;
                        $failed->save();

                        //revoke feature
                        $estate = EstateInfo::find($featured->listing_id);
                        if ($estate) {
                            $estate->feature = 0;
                            $estate->save();
                        }

                        //send failed billing email
                        $data = $featured;
                        $subject = 'Feature Auto Renew Failed Order ID: '.$featured->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your featured listing (ID: ".$featured->listing_id.") will be revoked.<br><br>Listing: $estate->name $estate->unit, $estate->city";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, 'Feature', $subject, $msg));

                        DB::table('features')
                            ->where('id', $featured->id)
                            ->update(['status' => 'renew_failed']);

                        continue;
                    }
                    
                    if ($response->status == 'succeeded') {
                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;

                        $feat = new Feature();
                        $feat->listing_id = $featured->listing_id;
                        $feat->user_id = $featured->user_id;
                        $feat->renew = $featured->renew;
                        $feat->ends_at = $ends_at;
                        $feat->period = $featured->period;
                        $feat->amount = $price;
                        $feat->stripe_id = $response->id;
                        $feat->cus_id = $paym->cus_id;
                        $feat->card_id = $card_id;
                        $feat->last4 =  $last4;
                        $feat->save();   

                        $estate = EstateInfo::find($featured->listing_id);
                        if ($estate) {
                            $estate->feature = 1;
                            $estate->save();
                        }

                        DB::table('features')->where('id', $featured->id)->update(['status' => 'renewed']);
                        
                        //send billing confirmation email
                        $data = $featured;
                        $data->listing_address = $estate->name.' '.$estate->unit.', '.$estate->city;

                        if ($estate->unit)
                            $data->listing_url = 'show/'.str_replace(' ','_',$estate->name).'/'.str_replace('#', '', str_replace(array(' ', '/'),'_',$estate->unit)).'/'.str_replace(' ','_',$estate->city);
                        else
                            $data->listing_url = 'show/'.str_replace(' ','_',$estate->name).'/_/'.str_replace(' ','_',$estate->city);

                        $subject = 'Feature Order ID: '.$feat->id;
                        Mail::to($user->email)->send(new \App\Mail\BillingMail($data, $domain, 'Feature', $subject));
                    }
                    else {  //payment failed, revoke featured
                        $estate = EstateInfo::find($featured->listing_id);
                        if ($estate) {
                            $estate->feature = 0;
                            $estate->save();
                        }

                        //send failed billing email
                        $data = $feat;
                        $subject = 'Feature Auto Renew Failed Order ID: '.$featured->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your featured listing (ID: ".$featured->listing_id.") will be revoked.<br><br>Listing: $estate->name $estate->unit, $estate->city";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, 'Feature', $subject, $msg));

                        DB::table('features')
                            ->where('id', $featured->id)
                            ->update(['status' => 'renew_failed']);
                    }
                }
            }
            else {
                if (substr($featured->ends_at, 0, 10) <= $today) {
                    $estate = EstateInfo::find($featured->listing_id);
                    if ($estate) {
                        $estate->feature = 0;
                        $estate->save();
                    }

                    DB::table('features')->where('id', $featured->id)->update(['status' => 'ended']);

                    $subject = 'Your Feature Listing Ended ID: '.$featured->id;
                    $msg = "Your featured listing (ID: ".$featured->listing_id.") ends today.<br><br>Listing: $estate->name $estate->unit, $estate->city";
                    Mail::to($user->email)->send(new \App\Mail\FailedMail($featured, $domain, 'Feature', $subject, $msg));
                }
            }
        }

        //putting premium checking here as well:
        $premiumed_listings = DB::table('premiums')->whereNotIn('status', ['renew_failed', 'ended', 'renewed'])->distinct()->get(['listing_id']);

        foreach ($premiumed_listings as $listing) {
            //echo $listing->listing_id.": "; 
            $premiumed = DB::table('premiums')->where('listing_id', '=', $listing->listing_id)->where('status', 'success')->orderBy('renew', 'DESC')->orderBy('ends_at', 'DESC')->get()->first();
            //echo $premiumed->ends_at."\n"; exit;

            if ($premiumed->renew == 1) { //check if it's time to charge(renew) again
                if (substr($premiumed->ends_at, 0, 10) <= $today) { //premium ends today, charge again
                    $user = User::find($premiumed->user_id);

                    if($premiumed->period == '1w'){
                        $price = env('PREM_1W');
                        $ends_at = Carbon::now()->addWeek();
                    }elseif ($premiumed->period == '2w'){
                        $price = env('PREM_2W');
                        $ends_at = Carbon::now()->addWeeks(2);
                    }elseif ($premiumed->period == '4w'){
                        $price = env('PREM_4W');
                        $ends_at = Carbon::now()->addWeeks(4);
                    }

                    if ($user->premium) {
                        if ($user->premium == 1)
                            $discount = env('SILV_PREM_DISC');
                        elseif ($user->premium == 2)
                            $discount = env('GOLD_PREM_DISC');
                        elseif ($user->premium == 3)
                            $discount = env('DIAM_PREM_DISC');
            
                        $price = $price * $discount;
                    }

                    $amount = $price * 100;

                    //get default payment method
                    $paym = DB::table('user_payments')->where('user_id', $user->id)->where('in_use', 1)->get()->first();   

                    try {
                        Stripe::setApiKey(env('STRIPE_SECRET'));
                        $response = Charge::create(array(
                            "amount" => $amount,   //e.g. 222 = 2.22
                            "currency" => "usd",
                            "customer" => $paym->cus_id
                        ));           
                    } catch (Card $e) {      
                        $body = $e->getJsonBody();
                        $err  = $body['error'];
                        $err_msg = $err['message'];
                        
                        //log failed payment
                        $failed = new Failed();
                        $failed->user_id = $user->id;
                        $failed->refer_id = $premiumed->listing_id;
                        $failed->type = 'premium';
                        $failed->renew = 1;
                        $failed->amount = $price;
                        $failed->period = $premiumed->period;
                        $failed->error = $err_msg;
                        $failed->save();

                        //revoke premium
                        $estate = EstateInfo::find($premiumed->listing_id);
                        if ($estate) {
                            $estate->premium = 0;
                            $estate->save();
                        }

                        //send failed billing email
                        $data = $premiumed;
                        $subject = 'Premium Auto Renew Failed Order ID: '.$premiumed->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your premium listing (ID: ".$premiumed->listing_id.") will be revoked.<br><br>Listing: $estate->name $estate->unit, $estate->city";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, 'Premium', $subject, $msg));

                        DB::table('premiums')
                            ->where('id', $premiumed->id)
                            ->update(['status' => 'renew_failed']);

                        continue;
                    }
                    
                    if ($response->status == 'succeeded') {
                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;

                        $prem = new Premium();
                        $prem->listing_id = $premiumed->listing_id;
                        $prem->user_id = $premiumed->user_id;
                        $prem->renew = $premiumed->renew;
                        $prem->ends_at = $ends_at;
                        $prem->period = $premiumed->period;
                        $prem->amount = $price;
                        $prem->stripe_id = $response->id;
                        $prem->cus_id = $paym->cus_id;
                        $prem->card_id = $card_id;
                        $prem->last4 = $last4;
                        $prem->save();   

                        $estate = EstateInfo::find($premiumed->listing_id);
                        if ($estate) {
                            $estate->premium = 1;
                            $estate->save();
                        }

                        DB::table('premiums')->where('id', $premiumed->id)->update(['status' => 'renewed']);
                        
                        //send billing confirmation email
                        $data = $prem;
                        $data->listing_address = $estate->name.' '.$estate->unit.', '.$estate->city;

                        if ($estate->unit)
                            $data->listing_url = 'show/'.str_replace(' ','_',$estate->name).'/'.str_replace('#', '', str_replace(array(' ', '/'),'_',$estate->unit)).'/'.str_replace(' ','_',$estate->city);
                        else
                            $data->listing_url = 'show/'.str_replace(' ','_',$estate->name).'/_/'.str_replace(' ','_',$estate->city);

                        $subject = 'Premium Order ID: '.$prem->id;
                        Mail::to($user->email)->send(new \App\Mail\BillingMail($data, $domain, 'Premium', $subject));
                    }
                    else {  //payment failed, revoke premium
                        $estate = EstateInfo::find($premiumed->listing_id);
                        if ($estate) {
                            $estate->premium = 0;
                            $estate->save();
                        }

                        //send failed billing email
                        $data = $premiumed;
                        $subject = 'Premium Auto Renew Failed Order ID: '.$premiumed->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your premium listing (ID: ".$premiumed->listing_id.") will be revoked.<br><br>Listing: $estate->name $estate->unit";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, 'Premium', $subject, $msg));

                        DB::table('premiums')
                            ->where('id', $premiumed->id)
                            ->update(['status' => 'renew_failed']);
                    }
                }
            }
            else {
                if (substr($premiumed->ends_at, 0, 10) <= $today) {
                    $estate = EstateInfo::find($premiumed->listing_id);
                    if ($estate) {
                        $estate->premium = 0;
                        $estate->save();
                    }

                    DB::table('premiums')->where('id', $premiumed->id)->update(['status' => 'ended']);

                    $subject = 'Your Premium Listing Ended ID: '.$premiumed->id;
                    $msg = "Your premium listing (ID: ".$premiumed->listing_id.") ends today.<br><br>Listing: $estate->name $estate->unit, $estate->city";
                    Mail::to($user->email)->send(new \App\Mail\FailedMail($premiumed, $domain, 'Premium', $subject, $msg));
                }
            }
        }

        //putting membership checking here as well:
        $memberships = DB::table('memberships')->whereNotIn('status', ['renew_failed', 'ended', 'renewed'])->distinct()->get(['user_id']);

        foreach($memberships as $membership){
            //echo $membership->user_id.": "; 
            $m_user = DB::table('memberships')->where('user_id', '=', $membership->user_id)->where('status', 'success')->orderBy('type', 'DESC')->orderBy('renew', 'DESC')->orderBy('ends_at', 'DESC')->get()->first();
            
            $created = new Carbon($m_user->created_at);
            $now = Carbon::now();
            $diff = $created->diff($now)->days;

            if ($diff && $diff % 30 == 0) {
                DB::table('users')->where('id', $membership->user_id)->update(['mail_count' => 0]);
            }

            if ($m_user->type == 1)
                $mtype = 'Silver';
            elseif ($m_user->type == 2)
                $mtype = 'Gold';
            elseif ($m_user->type == 3)
                $mtype = 'Diamond';

            if ($m_user->renew == 1) { //check if it's time to charge(renew) again
                if (substr($m_user->ends_at, 0, 10) <= $today) { //membership ends today, charge again
                    $user = User::find($m_user->user_id);

                    $p = explode('_', $m_user->period)[1];

                    if($p == '1m'){
                        if ($m_user->type == 1)
                            $price = env('SILV_1M');
                        elseif ($m_user->type == 2)
                            $price = env('GOLD_1M');
                        elseif ($m_user->type == 3)
                            $price = env('DIAM_1M');

                        $ends_at = Carbon::now()->addMonths(1);
                    }elseif ($p == '3m'){
                        if ($m_user->type == 1)
                            $price = env('SILV_3M');
                        elseif ($m_user->type == 2)
                            $price = env('GOLD_3M');
                        elseif ($m_user->type == 3)
                            $price = env('DIAM_3M');

                        $ends_at = Carbon::now()->addMonths(3);
                    }elseif ($p == '1y'){
                        if ($m_user->type == 1)
                            $price = env('SILV_1Y');
                        elseif ($m_user->type == 2)
                            $price = env('GOLD_1Y');
                        elseif ($m_user->type == 3)
                            $price = env('DIAM_1Y');

                        $ends_at = Carbon::now()->addYears(1);
                    }

                    $amount = $price * 100;

                    //get default payment method
                    $paym = DB::table('user_payments')->where('user_id', $user->id)->where('in_use', 1)->get()->first();   

                    try {
                        Stripe::setApiKey(env('STRIPE_SECRET'));
                        $response = Charge::create(array(
                            "amount" => $amount,   //e.g. 222 = 2.22
                            "currency" => "usd",
                            "customer" => $paym->cus_id 
                        ));
                    } catch (Card $e) {      
                        $body = $e->getJsonBody();
                        $err  = $body['error'];
                        $err_msg = $err['message'];
                        
                        //log failed payment
                        $failed = new Failed();
                        $failed->user_id = $user->id;
                        $failed->type = 'membership_'.$m_user->type;
                        $failed->refer_id = '';
                        $failed->renew = 1;
                        $failed->amount = $price;
                        $failed->period = $m_user->period;
                        $failed->error = $err_msg;
                        $failed->save();

                        //revoke membership
                        $user = User::find($m_user->user_id);
                        if ($user) {
                            $user->premium = 0;
                            $user->save();
                        }

                        //send failed billing email
                        $data = $m_user;

                        $subject = $mtype.' Membership Auto Renew Failed Order ID: '.$m_user->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your $mtype Membership will be revoked.";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, "$mtype Membership", $subject, $msg));

                        DB::table('memberships')
                            ->where('id', $m_user->id)
                            ->update(['status' => 'renew_failed']);

                        continue;
                    }
                    
                    if ($response->status == 'succeeded') {
                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;

                        $mem = new Membership();
                        //print_r($mem->period); exit;

                        $mem->user_id = $m_user->user_id;
                        $mem->renew = $m_user->renew;
                        $mem->ends_at = $ends_at;
                        $mem->period = $m_user->period;
                        $mem->amount = $price;
                        $mem->stripe_id = $response->id;
                        $mem->cus_id = $paym->cus_id;
                        $mem->card_id = $card_id;
                        $mem->last4 = $last4;
                        $mem->type = $m_user->type;
                        $mem->save();    

                        $user = User::find($m_user->user_id);
                        if ($user) {
                            $user->premium = $m_user->type;
                            $user->mail_count = 0;
                            $user->save();
                        }

                        $new_m = DB::table('memberships')->where('id', $m_user->id)->update(['status' => 'renewed']);
                        
                        //send billing confirmation email
                        $data = $mem;
                        
                        $data->period = $p;
                        $subject = $mtype.' Membership Order ID: '.$mem->id;
                        Mail::to($user->email)->send(new \App\Mail\BillingMail($data, $domain, "$mtype Membership", $subject));
                    }
                    else {  //payment failed, revoke membership
                        $user = User::find($m_user->user_id);
                        if ($user) {
                            $user->premium = 0;
                            $user->save();
                        }

                        //send failed billing email
                        $data = $m_user;
                        $subject = $mtype.' Membership Auto Renew Failed Order ID: '.$m_user->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your $mtype Membership will be revoked.";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, "$mtype Membership", $subject, $msg));

                        DB::table('memberships')
                            ->where('id', $m_user->id)
                            ->update(['status' => 'renew_failed']);
                    }
                }
            }
            else {
                if (substr($m_user->ends_at, 0, 10) <= $today) {
                    $user = User::find($m_user->user_id);
                    if ($user) {
                        $user->premium = 0;
                        $user->save();
                    }

                    DB::table('memberships')->where('id', $m_user->id)->update(['status' => 'ended']);

                    $subject = "Your $mtype Membership Ended ID: ".$m_user->id;
                    $msg = "Your $mtype Membership ends today.";
                    Mail::to($user->email)->send(new \App\Mail\FailedMail($m_user, $domain, "$mtype Membership", $subject, $msg));
                }
            }
        }

        //putting name_label checking here as well:
        $name_labels = DB::table('name_label')->whereNotIn('status', ['renew_failed', 'ended', 'renewed'])->distinct()->get(['building_id', 'type']);
        //print_r($name_labels); exit;
        foreach($name_labels as $name_label){
            $blabel = DB::table('name_label')->where('building_id', '=', $name_label->building_id)->where('type', $name_label->type)->where('status', 'success')->orderBy('renew', 'DESC')->orderBy('ends_at', 'DESC')->get()->first();        
            
            $user = User::find($blabel->user_id);

            if ($blabel->renew == 1) { //check if it's time to charge(renew) again
                if (substr($blabel->ends_at, 0, 10) <= $today) { //name label ends today, charge again     
                    if($blabel->period == '1y'){
                        if ($blabel->type == 'img')
                            $price = env('IMG_1Y');
                        elseif ($blabel->type == 'desc')
                            $price = env('DESC_1Y');

                        $ends_at = Carbon::now()->addYears(1);
                    }
                    elseif ($blabel->period == '2y'){
                        if ($blabel->type == 'img')
                            $price = env('IMG_2Y');
                        elseif ($blabel->type == 'desc')
                            $price = env('DESC_2Y');

                        $ends_at = Carbon::now()->addYears(2);
                    }
                    elseif ($blabel->period == '3y'){
                        if ($blabel->type == 'img')
                            $price = env('IMG_3Y');
                        elseif ($blabel->type == 'desc')
                            $price = env('DESC_3Y');

                        $ends_at = Carbon::now()->addYears(3);
                    }

                    $amount = $price * 100;

                    //get default payment method
                    $paym = DB::table('user_payments')->where('user_id', $user->id)->where('in_use', 1)->get()->first();   

                    try {
                        Stripe::setApiKey(env('STRIPE_SECRET'));
                        $response = Charge::create(array(
                            "amount" => $amount,   //e.g. 222 = 2.22
                            "currency" => "usd",
                            "customer" => $paym->cus_id 
                        ));
                    } catch (Card $e) {      
                        $body = $e->getJsonBody();
                        $err  = $body['error'];
                        $err_msg = $err['message'];
                        
                        //log failed payment
                        $failed = new Failed();
                        $failed->user_id = $user->id;
                        $failed->type = 'namelabel_'.$blabel->type;
                        $failed->refer_id = $blabel->building_id;
                        $failed->renew = 1;
                        $failed->amount = $price;
                        $failed->period = $blabel->period;
                        $failed->error = $err_msg;
                        $failed->save();

                        if ($blabel->type == 'img') { //ended, revoke name label
                            $type_desc = 'Building Images';

                            Building::where('building_id', $blabel->building_id)
                                ->update(['name_label' => 0]);
                                //->update(['name_label' => 0, 'path_for_name_label_image' => '', 'name_label_image' => '']);
                        } 
                        elseif ($blabel->type == 'desc') { //ended, revoke description
                            $type_desc = 'Building Description';

                            Building::where('building_id', $blabel->building_id)
                                ->update(['described' => 0]);     
                                //->update(['described' => 0, 'building_description' => '']);                    
                        } 

                        //send failed billing email
                        $data = $blabel;

                        $build =  DB::table('buildings')->where('building_id', $blabel->building_id)->get()->first();
                        $subject = $type_desc.' Auto Renew Failed Order ID: '.$blabel->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your $type_desc will be revoked.<br><br>Building: $build->building_name, $build->building_city";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, $type_desc, $subject, $msg));

                        DB::table('name_label')
                            ->where('id', $blabel->id)
                            ->update(['status' => 'renew_failed']);

                        continue;
                    }
                    
                    if ($response->status == 'succeeded2') {
                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;

                        DB::table('name_label')->insert(
                            [
                                'user_id' => $blabel->user_id, 
                                'building_id' => $blabel->building_id,
                                'renew' => 1,
                                'ends_at' => $ends_at,
                                'period' => $blabel->period,
                                'amount' => $price,
                                'stripe_id' => $response->id,
                                'cus_id' => $paym->cus_id,
                                'card_id' => $card_id,
                                'last4' => $last4,
                                'type' => $blabel->type,
                                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                            ]
                        );

                        $new_id = DB::getPdo()->lastInsertId();

                        $new_label = DB::table('name_label')->where('id', $new_id)->get()->first();

                        if ($blabel->type == 'img') { 
                            $type_desc = 'Building Images';

                            Building::where('building_id', $blabel->building_id)
                                ->update(['name_label' => $user->id]);
                        } 
                        elseif ($blabel->type == 'desc') { 
                            $type_desc = 'Building Description';

                            Building::where('building_id', $blabel->building_id)
                                ->update(['described' => $user->id]);                    
                        } 

                        DB::table('name_label')->where('id', $blabel->id)->update(['status' => 'renewed']);                      

                        //send billing confirmation email
                        $build =  DB::table('buildings')->where('building_id', $blabel->building_id)->get()->first();
                        $data = $new_label;                        
                        $data->building_address = $build->building_name.', '.$build->building_city;
                        $data->building_url = 'building/'.str_replace(array(' ', '/', '#', '?'),'_',$build->building_name).'/'.str_replace(' ','_',$build->building_city);
                        $subject = $type_desc.' Order ID: '.$new_id;
                        Mail::to($user->email)->send(new \App\Mail\BillingMail($data, $domain, $type_desc, $subject));
                    }
                    else {  //payment failed, revoke name label
                        if ($blabel->type == 'img') { //ended, revoke name label
                            $type_desc = 'Building Images';

                            Building::where('building_id', $blabel->building_id)
                                ->update(['name_label' => 0]);
                                //->update(['name_label' => 0, 'path_for_name_label_image' => '', 'name_label_image' => '']);
                        } 
                        elseif ($blabel->type == 'desc') { //ended, revoke description
                            $type_desc = 'Building Description';

                            Building::where('building_id', $blabel->building_id)
                                ->update(['described' => 0]);    
                                //->update(['described' => 0, 'building_description' => '']);                     
                        } 

                        //send failed billing email
                        $data = $blabel;

                        $build =  DB::table('buildings')->where('building_id', $blabel->building_id)->get()->first();
                        $subject = $type_desc.' Auto Renew Failed Order ID: '.$blabel->id;
                        $msg = "We are failed to auto recharge with your previous payment.<br><br>Your $type_desc will be revoked.<br><br>Building: $build->building_name, $build->building_city";
                        Mail::to($user->email)->send(new \App\Mail\FailedMail($data, $domain, $type_desc, $subject, $msg));

                        DB::table('name_label')
                            ->where('id', $blabel->id)
                            ->update(['status' => 'renew_failed']);
                    }
                }
            }
            else {
                if (substr($blabel->ends_at, 0, 10) <= $today) { //ended, revoke name label
                    if ($blabel->type == 'img') { 
                        $type_desc = 'Building Images';

                        Building::where('building_id', $blabel->building_id)
                            ->update(['name_label' => 0]);
                            //->update(['name_label' => 0, 'path_for_name_label_image' => '', 'name_label_image' => '']);
                    } 
                    elseif ($blabel->type == 'desc') { //ended, revoke name label
                        $type_desc = 'Building Description';

                        Building::where('building_id', $blabel->building_id)
                            ->update(['described' => 0]);       
                            //->update(['described' => 0, 'building_description' => '']);                  
                    } 

                    DB::table('name_label')->where('id', $blabel->id)->update(['status' => 'ended']);

                    $build =  DB::table('buildings')->where('building_id', $blabel->building_id)->get()->first();
                    $subject = $type_desc.' Ended Order ID: '.$blabel->id;
                    $msg = "Your $type_desc ends today.<br><br>Building: $build->building_name, $build->building_city";
                    Mail::to($user->email)->send(new \App\Mail\FailedMail($blabel, $domain, $type_desc, $subject, $msg));
                }
            }
        }

        //non-feature/premium rental listings will be disabled after 7 days
        $dayAgo = 7; // where here there is your calculation for now How many days
        $dayToCheck = Carbon::now()->subDays($dayAgo);
        //don't use user_updated_at!!!
        $expired_lists = DB::table('estate_data')->where('estate_type', 2)->where('active', 1)->where('feature', '<>', 1)->where('premium', '<>', 1)->where('paid_fee', '<>', 1)->where('amazon_id', 1)->where("updated_at", '<=', $dayToCheck)->get();
        //print_r($expired_lists); exit;

        foreach($expired_lists as $list) {
            $user = User::find($list->user_id);
            $subject = "Your Listing ID: $list->id is deactivated.";
            $msg = "7 days have pasted. Your Listing ID: $list->id is deactivated. If you want to continue, please <a href='".$domain."login'>login</a> and re-activate your listing.<br><br>Listing: $list->name $list->unit";

            Mail::to($user->email)->send(new \App\Mail\FailedMail($list, $domain, 'Deactivate', $subject, $msg));

            DB::table('estate_data')->where('id', $list->id)->update(['active' => 0]);
        }
    }
}
