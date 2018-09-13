<?php

namespace App\Repositories;

use App\AgentInfo;
use App\AgentsListings;
use App\AgentsBuildings;
use App\EstateInfo;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AgentRepo
{

    public function getAgentAll($id)
    {
        //return AgentInfo::with('user')->find($id);
        return AgentInfo::with('user')->where('id', $id)->get()->first();
    }

    public function getAgent($id)
    {
        //return AgentInfo::with('user')->find($id);
        return AgentInfo::with('user')->where('id', $id)
            ->where(function ($query) {
                $query->where('is_verified', 1)
                    ->orWhere('user_id', 0);
            })
            ->get()->first();
    }

    public function getAgentProfile($id)
    {
        $agent = array($this->getAgent($id));

        if(!isset($agent[0]) || !$agent[0]){
            return false;
        } else {
            $agent = $agent[0];
        }

        if (strlen($agent->photo) && $agent->photo != 'default/agent_no_photo_64x64.png'){

            if ($agent->user_id != 0){
                $agent->img = env('S3_IMG_PATH_1') . $agent->photo_url;
            } else {
                /*$hdrs = @get_headers(env('S3_IMG_PATH').$agent->photo);
                if(is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false){
                    $agent->img = env('S3_IMG_PATH').$agent->photo;
                } else*/
                    $agent->img = env('S3_IMG_PATH').$agent->photo;
            }

        } else {
            $agent->img = '/images/default_agent.jpg';
        }

        if (!empty($agent->logo_path)){
            if ($agent->user_id != 0) {
                $agent->path_to_logo = env('S3_IMG_PATH_1').$agent->logo_path;
            }
            else {
                $agent->path_to_logo = env('S3_IMG_PATH').$agent->logo_path;

                //if (sha1_file($agent->path_to_logo) == '288975e1875ae85b8acc90ea1a289e2eb10f14cf')
                    //$agent->path_to_logo = '';    
            }
        }else {
            $agent->path_to_logo = '';
        }

        if(!$agent->full_name){
            $agent->full_name = $agent->first_name . ' ' . $agent->last_name;
        }

        if($agent->user){
            $agent->email = $agent->user['email'];
        } else {
            $agent->email = '';
        }

        return $agent;
    }

    public function getAgentProfileByName($name)
    {
        $agent_infos = DB::table('agent_infos')->where(DB::raw('replace(replace(replace(full_name, "/", "_"), "#", "_"), " ", "_")'), $name)->get()->first();
        //print_r($agent_infos); exit;
        $id = $agent_infos->id;

        $agent = array($this->getAgent($id));

        if(!isset($agent[0]) || !$agent[0]){
            return false;
        } else {
            $agent = $agent[0];
        }

        if (strlen($agent->photo) && $agent->photo != 'default/agent_no_photo_64x64.png'){

            if ($agent->user_id != 0){
                $agent->img = env('S3_IMG_PATH_1') . $agent->photo_url;
            } else {
                /*$hdrs = @get_headers(env('S3_IMG_PATH').$agent->photo);
                if(is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false){
                    $agent->img = env('S3_IMG_PATH').$agent->photo;
                } else*/
                    $agent->img = env('S3_IMG_PATH').$agent->photo;
            }

        } else {
            $agent->img = '/images/default_agent.jpg';
        }

        if (!empty($agent->logo_path)){
            if ($agent->user_id != 0) {
                $agent->path_to_logo = env('S3_IMG_PATH_1').$agent->logo_path;
            }
            else {
                $agent->path_to_logo = env('S3_IMG_PATH').$agent->logo_path;

                //if (sha1_file($agent->path_to_logo) == '288975e1875ae85b8acc90ea1a289e2eb10f14cf')
                    //$agent->path_to_logo = '';    
            }
        }else {
            $agent->path_to_logo = '';
        }

        if(!$agent->full_name){
            $agent->full_name = $agent->first_name . ' ' . $agent->last_name;
        }

        if($agent->user){
            $agent->email = $agent->user['email'];
        } else {
            $agent->email = '';
        }

        return $agent;
    }

    public function agentsList()
    {
        return AgentInfo::orderBy('id', 'desc')->paginate(env('SEARCH_RESULTS_PER_PAGE'));
    }

    public function getAgentPhoto($agent){

        return DB::table('agents_buildings')
                ->join('buildings', 'agents_buildings.building_id', '=', 'buildings.building_id')
                ->join('agent_infos','agents_buildings.agent_id', '=', 'agent_infos.external_id')
                ->where('agents_buildings.agent_id', '=', $agent->external_id)
                ->select('buildings.b_listing_type')
                ->get();
    }

    public function updateAgent($request){
        //print_r($request->all()); exit;

        $agent = $this->getAgentAll($request->id);

        //print_r($agent); exit;

        if($request->hasFile('photo')){

            $image = $request->file('photo');

            $imageFileName = uniqid(time()) . '.' . $image->getClientOriginalExtension();

            $s3 = Storage::disk('s3');

            if(!empty($agent)){
                if($s3->exists($agent->photo_url))
                {
                    $s3->delete($agent->photo_url);
                }

            }

            $filePath = 'agent_images/'.$agent->user_id.'/' . $imageFileName;

            $s3->put($filePath, file_get_contents($image), 'public');

        }else{
            if(!empty($agent->photo) && $agent->photo_url){
                $imageFileName = $agent->photo;

                $filePath = $agent->photo_url;
            }else{
                $imageFileName = '';

                $filePath = '';
            }

        }

        $user_id = $agent->user_id;
        
        $agent->user_id = $user_id;
        $agent->last_name = $request->lastName;
        $agent->first_name = $request->firstName;
        $agent->photo = $imageFileName;
        $agent->photo_url = $filePath;
        $agent->company = $request->company;
        $agent->web_site = $request->webLink;
        $agent->office_phone = $request->officePhone;
        $agent->fax = $request->fax;
        $agent->description = $request->description;

        if (Auth::user()->isAdmin()){
            $agent->is_verified = $request->is_verified;
            if ($request->is_verified == 1) {                
                if ($request->is_verified != $request->old_verified) {
                    $user = User::find($user_id);
                    $domain = env('APP_URL');
                    $subject = "Your Agent Account is Approved!";
                    $url = $domain.'agent/'.str_replace(array(' ', '/', '#', '?'), '_', $agent->full_name).'/'.$agent->id;
                    $msg = "Congratulation! Your Agent Account is Approved!<br><br><a href='$url'>$url</a>";
                    Mail::to($user->email)->send(new \App\Mail\FailedMail($request, $domain, 'Listing', $subject, $msg));
                }
            }
            elseif ($request->is_verified == -1) {
                if ($request->is_verified != $request->old_verified) {
                    $user = User::find($user_id);
                    $domain = env('APP_URL');
                    $subject = "Sorry, Your Agent Account is Declined.";
                    $msg = "We're sorry. Your Agent Account is Declined.<br><br>Email: $user->email";
                    Mail::to($user->email)->send(new \App\Mail\FailedMail($request, $domain, 'Listing', $subject, $msg));
                }
            }
        }

        return $agent->save();
    }

    public function deleteAgent($request){

        $agent = $this->getAgent($request->id);

        if(!empty($agent)){
            return $agent->delete();
        }
    }

    public function deleteAgentImages($request){

        $agent = $this->getAgent($request->id);

        if($request->name == 'logo'){

            $path = $agent->logo_path;
        }

        if($request->name == 'images'){

            $image = $agent->photo;

            $path = $agent->photo_url;
        }

        if(Storage::disk('s3')->exists($path)) {
            //don't really delete it for now
            //Storage::disk('s3')->delete($path);
        }

        $image = '';

        $path = '';

        if($request->name == 'logo'){

            $agent->logo_path = $path;
        }

        if($request->name == 'images'){

            $agent->photo = $image;
            $agent->photo_url = $path;
        }

        return $agent->save();
    }

    public function getCurrentAgent($id)
    {
        return AgentInfo::where('user_id','=',$id)->with('user')->get();
    }

    public function getCurrentAllAgents($name, $unit, $city, $company='')
    {
        if ($company) {
            $listings = EstateInfo::with('logo')->with('images')
                    ->where('name', $name)->where('unit', $unit)->where('city', $city)->where('active', 1)->where('is_verified', 1)->where('agent_company', $company)
                    ->get();
        }
        else {
            $listings = EstateInfo::with('logo')->with('images')
                    ->where('name', $name)->where('unit', $unit)->where('city', $city)->where('active', 1)->where('is_verified', 1)
                    ->get();
        }

        $list_agents = array();
        foreach ($listings as $list) {
            $list_agents[] = $list->user_id;
        }

        //print_r($list_agents); exit;
        $all_agents = AgentInfo::whereIn('user_id', $list_agents)->where('is_verified', 1)->orderBy('company', 'asc')->with('user')->get();

        return $all_agents;
    }

    public function getListingAgentsByAddress($name, $unit, $city, $company='')
    {
        $listing_ids = array();

        if($company)
            $listings = DB::table('estate_data')->where('name', $name)->where('unit', $unit)->where('city', $city)->where('agent_company', $company)->get();
        else
            $listings = DB::table('estate_data')->where('name', $name)->where('unit', $unit)->where('city', $city)->get();

        foreach ($listings as $list) {
            $listing_ids[] = $list->id;
        }

        //print_r($listing_ids); exit;

        return AgentsListings::whereIn('listings_id', $listing_ids)->join('agent_infos', 'agent_id', '=', 'agent_infos.external_id')->groupBy('agent_infos.external_id')->orderBy('company', 'asc')->get();
    }

    public function getListingAgents($buidingId)
    {
        return AgentsListings::where(['listings_id' => $buidingId])->join('agent_infos', 'agent_id', '=', 'agent_infos.external_id')->get();
    }

    public function getBuildingAgents($id)
    {
        //return AgentsBuildings::where(['building_id' => $id])-join('agent_infos', 'agent_id', '=', 'agent_infos.external_id')->get();
        return array();
    }

    public function getAgentEstates($id, $name=''){
        if (!$id && $name) {
            $agent_infos = DB::table('agent_infos')->where(DB::raw('replace(replace(replace(full_name, "/", "_"), "#", "_"), " ", "_")'), $name)->get()->first();
            //print_r($agent_infos); exit;
            $id = $agent_infos->id;
        }

        $estates['sales'] = array();
        $estates['rentals'] = array();
        $estates['past_sales'] = array();
        $estates['past_rentals'] = array();

        $agent = AgentInfo::where('id', $id)->get();

        $user_id = $agent[0]->user_id;
        $agent_id = $agent[0]->external_id;

        if($user_id == 0){

            $listings = AgentsListings::select(DB::raw('agents_listings.*, if(status="-1", -4, status) as status_order'))
                                    ->join('agent_infos', 'agent_id', '=', 'agent_infos.external_id')
                                    ->join('estate_data', 'estate_data.listing_id', '=', 'listings_id')
                                    ->where('agent_infos.id', '=', $id)
                                    ->orderBy('status_order', 'desc')
                                    ->orderBy('estate_data.user_updated_at', 'desc')
                                    ->get();

            foreach ($listings as $listing){               

                $sale = EstateInfo::where('listing_id', '=', $listing['listings_id'])
                                    ->where(function ($query) {
                                        $query->where('active', '=', '1')
                                              ->orWhere('status', '<>', 1);
                                    })
                                    ->get();

                $images = array();
                if (isset($sale[0])) {
                    $images = $sale[0]->path_for_images;
                    $images = json_decode($images);
                }

                if (is_array($images) && count($images)){
                    $img = array_shift($images); //use first images
                    if (!empty($img)){ 
                        if ($sale[0]->estate_type==2){
                            if ($sale[0]->amazon_id == 0)
                                $sale[0]->img =  env('S3_IMG_PATH').$img;
                            elseif ($sale[0]->amazon_id == 1)
                                $sale[0]->img =  env('S3_IMG_PATH_1').$img;
                        }
                        elseif ($sale[0]->estate_type==1){
                            if ($sale[0]->amazon_id == 0)
                                $sale[0]->img =  env('S3_IMG_PATH').$img;
                            elseif ($sale[0]->amazon_id == 1)    
                                $sale[0]->img = env('S3_IMG_PATH_1').$img;
                        }
                    }
                }else{
                    if (isset($sale[0]))
                        $sale[0]->img = "/images/default_image_coming.jpg"; // not found img
                }

                if (isset($sale[0]))
                    $sale[0]->price = number_format($sale[0]->price,'0','.',',');

                if(isset($sale[0]) && !empty($sale[0])){
                    if($sale[0]['estate_type'] == '1'){
                        if ($sale[0]['status'] == '1')
                            $estates['sales'][] = $sale[0];
                        else
                            $estates['past_sales'][] = $sale[0];
                    }

                    if($sale[0]['estate_type'] == '2'){
                        if ($sale[0]['status'] == '1')
                            $estates['rentals'][] = $sale[0];
                        else
                            $estates['past_rentals'][] = $sale[0];
                    }
                }

            }

            //print_r($estates); exit;

        } else {
            $results = EstateInfo::select(DB::raw('estate_data.*, if(status="-1", -4, status) as status_order'))
                                ->leftjoin('agents_listings as al','al.listings_id', '=', 'estate_data.id')
                                ->leftjoin('agent_infos as a', 'a.external_id', '=', 'al.agent_id')
                                 ->where(function ($query) use ($user_id, $agent_id) {
                                    $query->where('estate_data.user_id','=',$user_id)
                                          ->orWhere('a.external_id', '=', $agent_id);
                                 })   
                                 ->where(function ($query) {
                                     $query->where('active', '=', '1')
                                           ->orWhere('status', '<>', 1);
                                 })
                                 ->orderBy('status_order', 'desc')
                                 ->orderBy('estate_data.user_updated_at', 'desc')
                                 ->get();            

            foreach ($results as $result){
                $images = $result->path_for_images;
                $images = json_decode($images);

                if (is_array($images) && count($images)){
                    $img = array_shift($images); //use first images
                    if (!empty($img)){
                        if ($result->estate_type==2){
                            if ($result->amazon_id == 0)
                                $result->img =  env('S3_IMG_PATH').$img;
                            elseif ($result->amazon_id == 1)
                                $result->img =  env('S3_IMG_PATH_1').$img;
                        }
                        elseif ($result->estate_type==1){
                            if ($result->amazon_id == 0)
                                $result->img =  env('S3_IMG_PATH').$img;
                            elseif ($result->amazon_id == 1)    
                                $result->img = env('S3_IMG_PATH_1').$img;
                        }
                    }
                }else{
                    $result->img = "/images/default_image_coming.jpg"; // not found img
                }

                $result->price = number_format($result->price,'0','.',',');

                if($result['estate_type'] == '1'){
                    if ($result['status'] == '1')
                        $estates['sales'][] = $result;
                    else
                        $estates['past_sales'][] = $result;
                }

                if($result['estate_type'] == '2'){
                    if ($result['status'] == '1')
                        $estates['rentals'][] = $result;
                    else
                        $estates['past_rentals'][] = $result;
                }
            }

        }

        return $estates;
    }

    public function searchAgents($search){

        $agents = AgentInfo::where(function ($query) {
                                $query->where('is_verified', 1)
                                    ->orWhere('user_id', 0);
                            })
                            ->where('company', '<>', 'Owner')
                            ->where('company', 'not like', '%management%')
                            ->where('full_name', 'not like', '%management%')
                            ->where(function ($query) use ($search) {
                                $query->where('full_name', 'like', '%' . $search . '%')
                                    ->orWhere(DB::raw('CONCAT(first_name, " " ,last_name)'), 'like', '%' . $search . '%');
                            })
                            ->paginate(env('SEARCH_RESULTS_PER_PAGE'))
                            ->appends(request()->query());

        foreach ($agents as $agent){
            if (strlen($agent->photo) && $agent->photo != 'default/agent_no_photo_64x64.png'){

                if ($agent->user_id !== 0){
                    $agent->img = env('S3_IMG_PATH_1') . $agent->photo_url;
                } else {
                    /*$hdrs = @get_headers(env('S3_IMG_PATH').$agent->photo);
                    if(is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false)
                        $agent->img = env('S3_IMG_PATH').$agent->photo;
                    else*/
                        $agent->img = env('S3_IMG_PATH').$agent->photo;
                }

            } else {
                $agent->img = '/images/default_agent.jpg';
            }

            if (!empty($agent->logo_path)){
                if ($agent->user_id != 0) {
                    $agent->path_to_logo = env('S3_IMG_PATH_1').$agent->logo_path;
                }
                else {
                    $agent->path_to_logo = env('S3_IMG_PATH').$agent->logo_path;
    
                    //if (sha1_file($agent->path_to_logo) == '288975e1875ae85b8acc90ea1a289e2eb10f14cf')
                        //$agent->path_to_logo = '';    
                }
            }else {
                $agent->path_to_logo = '';
            }

            if(!$agent->full_name){
                $agent->full_name = $agent->first_name . ' ' . $agent->last_name;
            }
        }

        return $agents;
    }

    public function searchAgentsList(){

        $agents = AgentInfo::select('agent_infos.*')
                            ->join('users', 'users.id', '=', 'agent_infos.user_id')
                            ->where(function ($query) {
                                $query->where('is_verified', 1)
                                    ->orWhere('user_id', 0);
                            })
                            ->where('company', '<>', 'Owner')
                            ->where('company', 'not like', '%management%')
                            ->where('full_name', 'not like', '%management%')
                            ->where('full_name', '<>', 'Denny Choi')
                            ->orderBy('full_name', 'asc')
                            ->paginate(env('SEARCH_RESULTS_PER_PAGE'))
                            ->appends(request()->query());

        foreach ($agents as $agent){
            if (strlen($agent->photo) && $agent->photo != 'default/agent_no_photo_64x64.png'){

                if ($agent->user_id !== 0){
                    $agent->img = env('S3_IMG_PATH_1') . $agent->photo_url;
                } else {
                    /*$hdrs = @get_headers(env('S3_IMG_PATH').$agent->photo);
                    if(is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false)
                        $agent->img = env('S3_IMG_PATH').$agent->photo;
                    else*/
                        $agent->img = env('S3_IMG_PATH').$agent->photo;
                }

            } else {
                $agent->img = '/images/default_agent.jpg';
            }

            if (!empty($agent->logo_path)){
                if ($agent->user_id != 0) {
                    $agent->path_to_logo = env('S3_IMG_PATH_1').$agent->logo_path;
                }
                else {
                    $agent->path_to_logo = env('S3_IMG_PATH').$agent->logo_path;
    
                    //if (sha1_file($agent->path_to_logo) == '288975e1875ae85b8acc90ea1a289e2eb10f14cf')
                        //$agent->path_to_logo = '';    
                }
            }else {
                $agent->path_to_logo = '';
            }

            if(!$agent->full_name){
                $agent->full_name = $agent->first_name . ' ' . $agent->last_name;
            }
        }

        return $agents;
    }


    public function autocomplete($search){

        $agents =  AgentInfo::select('id', 'last_name', 'first_name', 'full_name')
                        ->where('company', '<>', 'Owner')
                        ->where('company', 'not like', '%management%')
                        ->where('full_name', 'not like', '%management%')
                        ->where(function ($query) {
                            $query->where('is_verified', 1)
                                ->orWhere('user_id', 0);
                        })
                        ->where(function ($query) use ($search) {
                            $query->where('full_name', 'like', '%' . $search . '%')
                                ->orWhere(DB::raw('CONCAT(first_name, " " ,last_name)'), 'like', '%' . $search . '%');
                        })
                        ->limit(5)
                        ->get();

        $result = array();
        foreach ($agents as $agent){
            if ($agent->full_name) {
                $name1 = $agent->full_name;
                $name2 = str_replace(array(' ', '/', '#', '?'), '_', $agent->full_name);
                
            }
            else {
                $name1 = $agent->first_name . ' ' . $agent->last_name;
                $name2 = str_replace(array(' ', '/', '#', '?'), '_', $agent->first_name) . '_'. str_replace(array(' ', '/', '#', '?'), '_', $agent->last_name);
            }

            $result[] = array(
                'title' => $name1,
                //'link'  => '/agent/' . $name2 . '/' . $agent->id
                'link'  => '/agent/' . $name2
            );
        }

        return $result;
    }

}
