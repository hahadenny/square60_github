<?php

namespace App\Http\Controllers;

use App\Repositories\BuildingRepo;
use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Error\Card;
use App\Failed;
use Auth;

class BillingController extends Controller
{
    private $user;
    private $building;

    public function __construct(UserRepo $user, BuildingRepo $building)
    {
        $this->user = $user;
        $this->building = $building;
    }

    public function index(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        $user_id = $request->user()->id;

        $features = DB::table('features')
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'DESC')
                    ->get();

        $premiums = DB::table('premiums')
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'DESC')
                    ->get();

        $memberships = DB::table('memberships')
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'DESC')
                    ->get();

        $name_labels = DB::table('name_label')
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'DESC')
                    ->get();

        return view('billing' ,compact('features', 'premiums', 'memberships', 'name_labels'));
    }

    public function agent(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        $user_id = $request->user()->id;

        $user_payments = DB::table('user_payments')
                            ->where('user_id', $user_id)
                            ->orderBy('in_use', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->get();

        return view('agentBilling')->with('user_payments', $user_payments);
    }

    public function upgrade(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        $alreadyExpert = $this->user->userExpert($request->user()->id);
        //print_r($alreadyExpert); exit;

        $user_id = $request->user()->id;

        $user_payments = DB::table('user_payments')
                            ->where('user_id', $user_id)
                            ->orderBy('in_use', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->get();

        return view('upgrade', compact('alreadyExpert'))->with('user_payments', $user_payments);
    }

    public function upgradeForm(Request $request)
    {
        if ($request->post('stripeToken') || isset($request->paym) && $request->paym == 'prev' && isset($request->cus_id) && $request->cus_id)
        {
            //return $this->user->upgrateToExpert($request);
            $response = $this->user->upgrateToExpert($request);
            //print_r($response); exit;
            if ($response === 'failed') {
                return redirect('/upgrade')->with('status', '<span style="color:red">Sorry, we are failed to process your payment.</span>');
            }
            elseif($response){
                if ($request->type == 1)
                    $mtype = 'Silver';
                elseif ($request->type == 2)
                    $mtype = 'Gold';
                elseif ($request->type == 3)
                    $mtype = 'Diamond';

                return redirect('/upgrade')
                    ->with('status', "You are upgraded to be a $mtype Member successfully!");
            }else{
                return redirect('/upgrade')
                    ->with('status', '<span style="color:red;">Failed to upgrade, please try again later.</span>');
            }
        }
        else
        {
            echo('Something wrong');
            exit;
        }
    }
    public function expert(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        return view('expert');
    }

    public function nameLabel(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        $user_id = $request->user()->id;

        $buildings = $this->building->getWithoutNameLabelBuildings();

        $cur_buildings = $this->building->getCurrentNameLabelBuildings($request->user()->id);

        $user_payments = DB::table('user_payments')
                            ->where('user_id', $user_id)
                            ->orderBy('in_use', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->get();

        return view('nameLabel')->with('buildings',$buildings)->with('cur_buildings',$cur_buildings)->with('user_payments', $user_payments);
    }

    public function nameLabelImage(Request $request){
        if (Auth::guest()) {
            return view('error')->with('msg', 'Please <a href="/register">sign up</a> or <a href="/login">login</a> as an agent in order to view this example.');
        }

        $request->user()->authorizeRoles(['Agent']);

        return view('nameLabelImage');
    }

    public function nameLabelDescription(Request $request){
        if (Auth::guest()) {
            return view('error')->with('msg', 'Please <a href="/register">sign up</a> or <a href="/login">login</a> as an agent in order to view this example.');
        }

        $request->user()->authorizeRoles(['Agent']);

        return view('nameLabelDescription');
    }

    public function saveNameLabel(Request $request)
    {//TODO add stripe id
        //dd($request->post());

        //print_r($request->all()); exit;
        //print_r($request->namelabel); exit;

        $user = $request->user();
        $renew = isset($request->renew) ? 1 : 0;        
        $domain = env('APP_URL');

        $amount = array();
        $customer = false;
        $first = true;
        foreach ($request->namelabel as $item){ 
            if(isset($item['imagePrice'])){
                if($item['imagePrice'] == '1y'){
                    $imageEndDate = Carbon::now()->addYear(1);
                    $price = env('IMG_1Y');
                }elseif($item['imagePrice'] == '2y'){
                    $imageEndDate = Carbon::now()->addYears(2);
                    $price = env('IMG_2Y');
                }elseif($item['imagePrice'] == '3y'){
                    $imageEndDate = Carbon::now()->addYears(3);
                    $price = env('IMG_3Y');
                }

                $amount[] = $price;
                $cprice = $price * 100;

                try {     
                    if (isset($request->paym) && $request->paym == 'prev') {            
                        Stripe::setApiKey(env('STRIPE_SECRET'));
                        $response = Charge::create(array(
                            "amount" => $cprice,   //222 = 2.22
                            "currency" => "usd",
                            "customer" => $request->cus_id 
                        ));
        
                        if ($response->status != 'succeeded') {
                            return 'failed';                
                        }
        
                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;
                        $cus_id = $request->cus_id;
        
                        //set default payment
                        DB::table('user_payments')->where('user_id', $user->id)->update(['in_use' => 0]);
                        DB::table('user_payments')->where('user_id', $user->id)->where('cus_id', $cus_id)->update(['in_use' => 1]);
                    }
                    else {               
                        if (!$customer) {
                            $options = array('email'=>$user->email);
                            $customer = $user->createAsStripeCustomer($request->post('stripeToken'), $options);
                            $response = $user->charge($cprice);
                        }
                        else {
                            Stripe::setApiKey(env('STRIPE_SECRET'));
                            $response = Charge::create(array(
                                "amount" => $cprice,   //e.g. 222 = 2.22
                                "currency" => "usd",
                                "customer" => $customer->id
                            ));                     
                        }      
                        
                        //print_r($response->status); exit;
            
                        if ($response->status != 'succeeded') {
                            return redirect('/billing/namelabel')
                                    ->with('status', '<span style="color:red;">Failed to process payment. Please try again later.</span>');           
                        }       

                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;
                        $cus_id = $customer->id;

                        //set default payment
                        if ($first) {
                            $first = false;
                            DB::table('user_payments')->where('user_id', $user->id)->update(['in_use' => 0]);
                            DB::table('user_payments')->insert(
                                [
                                    'user_id' => $user->id, 
                                    'stripe_id' => $response->id,
                                    'cus_id' => $customer->id,
                                    'card_id' => $card_id,
                                    'card_brand' => $response->source->brand,
                                    'last4' => $last4,
                                    'in_use' => 1
                                ]
                            );
                        }
                    }
                } catch (Card $e) {
                    $body = $e->getJsonBody();
                    $err  = $body['error'];
                    $err_msg = $err['message'];
                    
                    //log failed payment
                    $failed = new Failed();
                    $failed->user_id = $user->id;
                    $failed->refer_id = $item['address'];
                    $failed->type = 'namelabel_img';
                    $failed->renew = $renew;
                    $failed->amount = $price;
                    $failed->period = $item['imagePrice'];
                    $failed->error = $err_msg;
                    $failed->save();
        
                    return redirect('/billing/namelabel')
                            ->with('status', '<span style="color:red;">Failed to process payment. Please try again later.</span>');           
                }

                $this->user->nameLabel($user->id, $item['address'], $response->id, $cus_id, $card_id, $last4, 'img', $item['imagePrice'], $price, $imageEndDate, $renew);

                $new_id = DB::getPdo()->lastInsertId();

                if(!empty($item['image'])){
                    $this->building->addNameLabelImages($item['address'],$item['image'], $request->user()->id);
                }
                else {
                    DB::table('buildings')
                        ->where('building_id', $item['address'])
                        ->update(['name_label' => $user->id]);
                }

                //send billing confirmation email
                $build =  DB::table('buildings')->where('building_id', $item['address'])->get()->first();
                $data = DB::table('name_label')->where('id', $new_id)->get()->first();
                $data->building_address = $build->building_name.', '.$build->building_city;
                $data->building_url = 'building/'.str_replace(array(' ', '/', '#', '?'),'_',$build->building_name).'/'.str_replace(' ','_',$build->building_city);
                $subject = 'Building Images Order ID: '.$new_id;
                Mail::to($user->email)->send(new \App\Mail\BillingMail($data, $domain, 'Building Images', $subject));
            }            

            if(isset($item['descriptionPrice'])){
                if($item['descriptionPrice'] == '1y'){
                    $descriptionEndDate = Carbon::now()->addYear(1);
                    $price = env('DESC_1Y');
                }elseif($item['descriptionPrice'] == '2y'){
                    $descriptionEndDate = Carbon::now()->addYears(2);
                    $price = env('DESC_2Y');
                }elseif($item['descriptionPrice'] == '3y'){
                    $descriptionEndDate = Carbon::now()->addYears(3);
                    $price = env('DESC_3Y');
                }

                $amount[] = $price;
                $cprice = $price * 100;

                try {
                    if (isset($request->paym) && $request->paym == 'prev') {            
                        Stripe::setApiKey(env('STRIPE_SECRET'));
                        $response = Charge::create(array(
                            "amount" => $cprice,   //222 = 2.22
                            "currency" => "usd",
                            "customer" => $request->cus_id 
                        ));
        
                        if ($response->status != 'succeeded') {
                            return 'failed';                
                        }
        
                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;
                        $cus_id = $request->cus_id;
        
                        //set default payment
                        DB::table('user_payments')->where('user_id', $user->id)->update(['in_use' => 0]);
                        DB::table('user_payments')->where('user_id', $user->id)->where('cus_id', $cus_id)->update(['in_use' => 1]);
                    }
                    else {
                        if (!$customer) {
                            $options = array('email'=>$user->email);
                            $customer = $user->createAsStripeCustomer($request->post('stripeToken'), $options);
                            $response = $user->charge($cprice);
                        }
                        else {
                            Stripe::setApiKey(env('STRIPE_SECRET'));
                            $response = Charge::create(array(
                                "amount" => $cprice,   //e.g. 222 = 2.22
                                "currency" => "usd",
                                "customer" => $customer->id
                            ));                     
                        } 
                        
                        //print_r($response->status); exit;
            
                        if ($response->status != 'succeeded') {
                            return redirect('/billing/namelabel')
                                    ->with('status', '<span style="color:red;">Failed to process payment. Please try again later.</span>');           
                        }      
                        
                        $card_id = $response->source->id;
                        $last4 = $response->source->last4;
                        $cus_id = $customer->id;

                        //set default payment
                        if ($first) {
                            $first = false;
                            DB::table('user_payments')->where('user_id', $user->id)->update(['in_use' => 0]);
                            DB::table('user_payments')->insert(
                                [
                                    'user_id' => $user->id, 
                                    'stripe_id' => $response->id,
                                    'cus_id' => $customer->id,
                                    'card_id' => $card_id,
                                    'card_brand' => $response->source->brand,
                                    'last4' => $last4,
                                    'name' => $request->name,
                                    'address' => $request->address,
                                    'city' => $request->city,
                                    'state' => $request->state,
                                    'zip' => $response->source->address_zip,
                                    'in_use' => 1
                                ]
                            );
                        }
                    }
                } catch (Card $e) {
                    $body = $e->getJsonBody();
                    $err  = $body['error'];
                    $err_msg = $err['message'];
                    
                    //log failed payment
                    $failed = new Failed();
                    $failed->user_id = $user->id;
                    $failed->refer_id = $item['address'];
                    $failed->type = 'namelabel_desc';
                    $failed->renew = $renew;
                    $failed->amount = $price;
                    $failed->period = $item['descriptionPrice'];
                    $failed->error = $err_msg;
                    $failed->save();
        
                    return redirect('/billing/namelabel')
                            ->with('status', '<span style="color:red;">Failed to process payment. Please try again later.</span>');           
                }

                $this->user->nameLabel($user->id, $item['address'], $response->id, $cus_id, $card_id, $last4, 'desc', $item['descriptionPrice'], $price, $descriptionEndDate, $renew);

                $new_id = DB::getPdo()->lastInsertId();

                if(!empty($item['description'])){
                    $this->building->addDescription($item['address'],$item['description'],$user->id);
                }   
                else {
                    DB::table('buildings')
                        ->where('building_id', $item['address'])
                        ->update(['described' => $user->id]);
                }

                //send billing confirmation email
                $build =  DB::table('buildings')->where('building_id', $item['address'])->get()->first();
                $data = DB::table('name_label')->where('id', $new_id)->get()->first();
                $data->building_address = $build->building_name.', '.$build->building_city;
                $data->building_url = 'building/'.str_replace(array(' ', '/', '#', '?'),'_',$build->building_name).'/'.str_replace(' ','_',$build->building_city);
                $subject = 'Building Description Order ID: '.$new_id;
                Mail::to($user->email)->send(new \App\Mail\BillingMail($data, $domain, 'Building Description', $subject));
            }                    
        }

        $total = array_sum($amount);

        if($this->user->chargeNameLabel($request->user()->id, $total)){
            return redirect('/billing/namelabel')
                ->with('status', 'Congratulation! You just added a name label.');
        }else{
            return redirect('/billing/namelabel')
                ->with('status', '<span style="color:red;">Failed to add name label.</span>');
        }


    }

    public function updateNameLabel(Request $request) {
        if(!empty($request->curnamelabel)){
            $this->building->addNameLabelImages($request->building_id, $request->curnamelabel, $request->user()->id);
        }

        if(!empty($request->curdescription)){
            $this->building->addDescription($request->building_id, $request->curdescription, $request->user()->id);
        } 

        return redirect('/billing/namelabel')->with('status', 'Data updated successfully!');
    }

    public function premium(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        $user_id = $request->user()->id;

        $user_payments = DB::table('user_payments')
                            ->where('user_id', $user_id)
                            ->orderBy('in_use', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->get();

        return view('premium')->with('user_payments', $user_payments);
    }

    public function premiuming(Request $request){
        if ($request->post('stripeToken') || isset($request->paym) && $request->paym == 'prev' && isset($request->cus_id) && $request->cus_id){
            $response = $this->user->premiuming($request);
            //print_r($response); exit;
            if ($response === 'failed') {
                return redirect('/premium?id='.$request->id)->with('status', '<span style="color:red;">Sorry, we are failed to process your payment.</span>');
            }
            elseif($response){
                return redirect('/home/listing')
                    ->with('status', 'Your listing is premium now!');
            }else{
                return redirect('/home/listing')
                    ->with('status', '<span style="color:red;">Failed to upgrade listing, please try again later.</span>');
            }
        }else{
            echo('Something wrong!');
            exit;
        }
    }

    public function feature(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        $user_id = $request->user()->id;

        $user_payments = DB::table('user_payments')
                            ->where('user_id', $user_id)
                            ->orderBy('in_use', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->get();

        return view('feature')->with('user_payments', $user_payments);
    }

    public function featuring(Request $request){
        if ($request->post('stripeToken') || isset($request->paym) && $request->paym == 'prev' && isset($request->cus_id) && $request->cus_id){
            $response = $this->user->featuring($request);
            //print_r($response); exit;
            if ($response === 'failed') {
                return redirect('/feature?id='.$request->id)->with('status', '<span style="color:red;">Sorry, we are failed to process your payment.</span>');
            }
            elseif($response){
                return redirect('/home/listing')
                    ->with('status', 'Your listing is featured successfully!');
            }else{
                return redirect('/home/listing')
                    ->with('status', '<span style="color:red;">Failed to feature listing, please try again later.</span>');
            }
        }else{
            echo('Something wrong!');
            exit;
        }
    }

    public function managePaym(Request $request) {
        if ($request->action == 'update') {
            $user_id = $request->user()->id;

            DB::table('user_payments')->where('user_id', $user_id)->update(['in_use' => 0]);
            DB::table('user_payments')->where('user_id', $user_id)->where('cus_id', $request->cus_id)->update(['in_use' => 1]);

            return redirect('/billing/agent')
                ->with('status', 'Payment method updated.');
        }
        if ($request->action == 'delete') {
            $user_id = $request->user()->id;

            DB::table('user_payments')->where('user_id', $user_id)->where('cus_id', $request->cus_id)->delete();

            $cur_def = DB::table('user_payments')->where('user_id', $user_id)->orderBy('in_use', 'DESC')->orderBy('id', 'DESC')->get()->first();
            
            DB::table('user_payments')->where('user_id', $user_id)->where('id', $cur_def->id)->update(['in_use' => 1]);

            return redirect('/billing/agent')
                ->with('status', 'Payment method deleted.');
        }
        elseif ($request->action == 'add' && $request->post('stripeToken')) {
            $response = $this->user->addPaym($request);

            if ($response === 'failed') {
                return redirect('/billing/agent')->with('status', '<span style="color:red;">We\'re failed to add your payment method.</span>');
            }
            elseif($response){
                return redirect('/billing/agent')
                    ->with('status', 'Payment method added successfully!');
            }
        }
    }
}
