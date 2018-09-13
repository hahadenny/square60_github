<?php
namespace App\Http\Controllers;

use App\Repositories\EstateInfoRepo;
use App\Repositories\BuildingRepo;
use App\Repositories\SearchRepo;
use App\Repositories\ShowRepo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class SendController extends Controller
{
    private $estate;
    private $search;
    private $building;
    private $showRepo;

    public function __construct(EstateInfoRepo $estate,SearchRepo $search,BuildingRepo $building,ShowRepo $showRepo)
    {
        $this->estate = $estate;
        $this->search = $search;
        $this->building = $building;
        $this->showRepo = $showRepo;
    }

    public function sendEmail(Request $request)
    {
        if(!empty($request)){
            $data = $request;

            $validator = Validator::make($data->all(), [
                'useremail' => 'required|email',
            ]);
    
            if ($validator->fails()) {
                return 'Please enter a valid email.';
            }
            
            //URL::route('main')
            if ($data['type'] == 'building') {
                $advertData = $this->building->getBuildingById($data['listingid']);
                if($advertData){
                    $data['advertLink'] = env('APP_URL').'building/'.str_replace(' ','_',$advertData['building_name']).'/'.str_replace(' ','_',$advertData['building_city']);
                }
            }
            else {
                $advertData = $this->estate->findListing($data['listingid']);
                if($advertData){
                    if ($advertData['unit'])
                        $data['advertLink'] = env('APP_URL').'show/'.str_replace(' ','_',$advertData['name']).'/'.str_replace('#', '', str_replace(array(' ', '/'),'_',$advertData['unit'])).'/'.str_replace(' ','_',$advertData['city']).'/'.$data['listingid'];
                    else
                        $data['advertLink'] = env('APP_URL').'show/'.str_replace(' ','_',$advertData['name']).'/_/'.str_replace(' ','_',$advertData['city']).'/'.$data['listingid'];
                }
            }
            if(!empty($data['useremail']) && !empty($data['message'])){

                //only 10 emails per hour
                if (!isset($_COOKIE['message'])) {
                    setcookie("message", 1, time()+3600);
                    $_COOKIE['message'] = 1;
                }
                else {
                    $_COOKIE['message'] = $_COOKIE['message']+1;
                }

                if ($_COOKIE['message'] > 10)
                    return $response = 'Sorry, 10 messages allowed every hour.';

                if(!empty($data['agentemail'])){
                    $domain = env('APP_URL');
                    
                    $data['subject'] = $advertData['name'];
                    if ($advertData['unit'])
                        $data['subject'] .= ' '.$advertData['unit'];
                    $data['subject'] .= ' '.$advertData['city'];

                    $emails = explode(',', $data['agentemail']);
                    foreach ($emails as $secondEmail){
                        $secondEmail = trim($secondEmail);
                        //$secondEmail = 'denny0709@hotmail.com'; //for testing only!!!!
                        if ($secondEmail) {
                            //check if user want forward email
                            $agent_info = DB::table('agent_infos')->where('email', $secondEmail)->get()->first();

                            if (!$agent_info || !isset($agent_info->forward_email)) {
                                $agent_user = DB::table('users')->where('email', $secondEmail)->get()->first();
                                if ($agent_user && isset($agent_user->id))
                                    $agent_info = DB::table('agent_infos')->where('user_id', $agent_user->id)->get()->first();
                            }

                            if ($agent_info && isset($agent_info->forward_email) && $agent_info->forward_email) {
                                $data['agent_user_id'] = $agent_info->user_id;
                                Mail::to($secondEmail)->send(new \App\Mail\SendEmail($data, $domain, $secondEmail));
                            }
                            elseif(!count($agent_info)) {
                                Mail::to($secondEmail)->send(new \App\Mail\SendEmail($data, $domain, $secondEmail));
                            }
                        }
                    }
                }else{ 
                    $emails = env('DEMO_EMAIL');
                    $emails = explode(',',$emails);
                    $domain = env('APP_URL');
                    
                    $data['subject'] = $advertData['name'];
                    if ($advertData['unit'])
                        $data['subject'] .= ' '.$advertData['unit'];
                    $data['subject'] .= ' '.$advertData['city'];

                    foreach ($emails as $secondEmail) {        
                        $secondEmail = trim($secondEmail);
                        if ($secondEmail) {
                            //check if user want forward email
                            $agent_info = DB::table('agent_infos')->where('email', $secondEmail)->get()->first();

                            if (!$agent_info || !isset($agent_info->forward_email)) {
                                $agent_user = DB::table('users')->where('email', $secondEmail)->get()->first();
                                if ($agent_user && isset($agent_user->id))
                                    $agent_info = DB::table('agent_infos')->where('user_id', $agent_user->id)->get()->first();
                            }

                            if ($agent_info && isset($agent_info->forward_email) && $agent_info->forward_email) {
                                $data['agent_user_id'] = $agent_info->user_id;
                                Mail::to($secondEmail)->send(new \App\Mail\SendEmail($data, $domain, $secondEmail));
                            }
                        }
                    }
                }
                return $response = 'Your message is sent successfully!';
            }else{
                return $response = 'Please fill in all data.';
            }
        }else{
            return $response = 'Error, message didn\'t send.';
        }
    }

    public function sendResult(Request $request)
    {

        if(!empty($request->post())){

            $email = $request->email;

            if(empty($email)){
                return $response = 'Please enter your email.';
            }

            $result = $this->search->getSearchresults($request->searchid);

            $ids = $result->ids;

            $idsArray = array();
            if (strlen($ids)){
                $idsArray = explode(',',$ids);
            }
            $model = $this->estate->queryEstate();
            if (count($idsArray)){
                $model->whereIn('id',$idsArray);
            }else {
                $requestData = $result->request;
                $requestData = json_decode($requestData,true);

                $this->search->_prepareModel($model,$requestData);
            }

            $results = $model->take(env('SEARCH_RESULTS_PER_PAGE'))->orderBy('estate_data.created_at','desc')->with('openHouse')->get();

            $results = $this->search->_prepareResults($results);

            //$domain = URL::route('main');
            $domain = env('APP_URL');
            if ($results[0]->estate_type==2)
                $subject = "Search Results";
            else
                $subject = "Search Results";
            Mail::to($email)->send(new \App\Mail\ResultMail($results, $domain, $subject, '', $request->searchid));

            return $response = 'Result sent.';

        }else{
            return $response = 'Error, result didn\'t send.';
        }
    }

    public function sendList(Request $request)
    {

        if(!empty($request->post())){
            $email = $request->email;

            if(empty($email)){
                return $response = 'Please enter your email.';
            }

            $details = $this->showRepo->show($request, '', $request->listing_id);
            $details['result']->img = $details['images'][0];

            //return $details['result']->img;  //for testing

            $domain = env('APP_URL');
            if ($details['result']->estate_type==2)
                $subject = "Listing: ".$details['result']->full_address.' '.$details['result']->unit;
            else
                $subject = "Listing: ".$details['result']->full_address.' '.$details['result']->unit;

            Mail::to($email)->send(new \App\Mail\ListMail($details['result'], $details['agents'], $domain, $subject));

            return $response  = 'List sent.';
        }else{
            return $response = 'Error, list didn\'t send.';
        }
    }

    public function sendBuilding(Request $request)
    {

        if(!empty($request->post())){
            $email = $request->email;

            if(empty($email)){
                return $response = 'Please enter your email.';
            }

            $result = $this->building->getBuilding($request->building_name, $request->building_city);
            $details = $this->showRepo->showBuilding($result, '', '');
            $details['result']->img = $details['images'][0];

            //return $details['result']->img;  //for testing

            $domain = env('APP_URL');
            $subject = "Building: ".$details['result']->building_name;

            Mail::to($email)->send(new \App\Mail\BuildingMail($details['result'], $details['agents'], $domain, $subject));

            return $response  = 'Building sent.';
        }else{
            return $response = 'Error, list didn\'t send.';
        }
    }

    public function sendInvitation(){

       $users = User::with('userAgent')->get();

       foreach($users as $user)
       {
           if(!empty($user->userAgent))
           {
               Mail::to($user->email)->send(new \App\Mail\InvitationMail($user));
           }
       }
    }
}
