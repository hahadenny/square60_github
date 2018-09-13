<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Repositories\BuildingRepo;
use App\Repositories\FilterRepo;
use App\Repositories\EstateInfoRepo;
use App\Repositories\OpenHouseRepo;

class RentalController extends Controller
{
    private $building;
    private $filter;
    private $estate;
    private $open;

    public function __construct(BuildingRepo $building, FilterRepo $filter, EstateInfoRepo $estateRepo, OpenHouseRepo $openRepo)
    {
        $this->building = $building;
        $this->filter = $filter;
        $this->estate = $estateRepo;
        $this->open = $openRepo;
    }

    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['Owner', 'Agent', 'Admin', 'man']);

        $data = $this->filter->dataFilters();

        $openHours = $this->open->getHours();

        return view('rental', $data)->with('openHours', $openHours);
    }

    public function store(Request $request)
    {
        $request->user()->authorizeRoles(['Owner', 'Agent', 'Admin', 'man']);

        if($request->has('street_address')){

            if(isset($request->model) && $request->model == 1  && isset($request->id)){
                $estate = $this->estate->findListing($request->id);
            }elseif(isset($request->model) && $request->model == 0){
                $estate = $this->estate->newListing();
            }

            $rules = [
                'boro' => 'required',
                'district' => 'required',
                'street_address' => 'required|min:3',
                'city' => 'required',
                'apartment' => in_array($request->type, array('6', '7', '9', '40')) ? 'required' : '',
                'ny' => 'required',
                'zip' => 'required|numeric',
                'type' => 'required',
                'size' => in_array($request->type, array('6')) ? 'required|numeric' : '',
                'bed' => 'required|numeric',
                'bath' => 'required|numeric',
                'room' => 'required|numeric',
                'description' => 'required',
                'price' => 'required|numeric',
                'fees' => 'required',
                //'broker' => 'required',
                //'filters' => 'required',
                //'year_built' => $request->year_built ? 'numeric' : '',
                //'feature' => 'required',
                //'image' => empty($estate->images) ? 'required' : '',
                'image' => 'array|min:0|max:20',
                'plans' => 'array|min:0|max:20',
                'condition' => 'required'
            ];

            $images = $request->image;
            $plans = $request->plans;

            if (!empty($images)) {
                foreach ($images as $key => $image) { // add individual rules to each image                    
                    $rules[sprintf('image.%d', $key)] = 'required|image|max:20480';
                }
            }

            if (!empty($plans)) {
                foreach ($plans as $key => $plan) { // add individual rules to each image                    
                    $rules[sprintf('plans.%d', $key)] = 'required|image|max:20480';
                }
            }

            //if (Auth::user()->isAgent() || Auth::user()->isMan()) {
            if (Auth::user()->isAgent() && !$request->name_on_list) {
                $rules['agreement'] = 'array|min:1|max:20';

                $agreements = $request->agreement;

                if (!empty($agreements)) {
                    foreach ($agreements as $key => $agreement) { // add individual rules to each image                    
                        $rules[sprintf('agreement.%d', $key)] = 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:20480';
                    }
                }
            }

            if (Auth::user()->isOwner()) {
                $rules['deed'] = 'array|min:1|max:20';
                $rules['utilitybill'] = 'array|min:1|max:20';
                $rules['photoid'] = 'array|min:1|max:20';

                $deeds = $request->deed;
                $utilitybills = $request->utilitybill;
                $photoids = $request->photoid;

                if (!empty($deeds)) {
                    foreach ($deeds as $key => $deed) { // add individual rules to each image                    
                        $rules[sprintf('deed.%d', $key)] = 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:20480';
                    }
                }

                if (!empty($utilitybills)) {
                    foreach ($utilitybills as $key => $utilitybill) { // add individual rules to each image                    
                        $rules[sprintf('utilitybill.%d', $key)] = 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:20480';
                    }
                }

                if (!empty($photoids)) {
                    foreach ($photoids as $key => $photoid) { // add individual rules to each image                    
                        $rules[sprintf('photoid.%d', $key)] = 'required|mimes:jpeg,bmp,png,gif,svg,pdf|max:20480';
                    }
                }
            }

            $validator = Validator::make($request->all(), $rules);

            $first_err = $validator->errors()->first();

            $img_err = '';
            if (preg_match('/The image\./', $first_err)) {
                $img_err = preg_replace('/\.(\d+) /', ' ', $first_err);
            }

            $plan_err = '';
            if (preg_match('/The plans\./', $first_err)) {
                $plan_err = preg_replace('/\.(\d+) /', ' ', $first_err);
            }

            $agreement_err = '';
            if (preg_match('/The agreement\./', $first_err)) {
                $agreementn_err = preg_replace('/\.(\d+) /', ' ', $first_err);
            }

            $deed_err = '';
            if (preg_match('/The deed\./', $first_err)) {
                $deed_err = preg_replace('/\.(\d+) /', ' ', $first_err);
            }

            $utilitybill_err = '';
            if (preg_match('/The utilitybill\./', $first_err)) {
                $utilitybill_err = preg_replace('/\.(\d+) /', ' ', $first_err);
            }

            $photoid_err = '';
            if (preg_match('/The photoid\./', $first_err)) {
                $photoid_err = preg_replace('/\.(\d+) /', ' ', $first_err);
            }

            if ($validator->fails()) {
                return redirect('/home/listing/rental')
                    ->withErrors($validator)
                    ->withInput()
                    ->with('status', 'Failed to submit listing.')
                    ->with('img_err', $img_err)
                    ->with('plan_err', $plan_err)
                    ->with('agreement_err', $agreement_err)
                    ->with('deed_err', $deed_err)
                    ->with('utilitybill_err', $utilitybill_err)
                    ->with('photoid_err', $photoid_err);
            }

            /*$sql = "DROP FUNCTION IF EXISTS alphanum; 
                        CREATE FUNCTION alphanum( str CHAR(255) ) RETURNS CHAR(255) DETERMINISTIC
                        BEGIN 
                        DECLARE i, len SMALLINT DEFAULT 1; 
                        DECLARE ret CHAR(255) DEFAULT ''; 
                        DECLARE c CHAR(1); 
                        SET len = CHAR_LENGTH( str ); 
                        REPEAT 
                            BEGIN 
                            SET c = MID( str, i, 1 ); 
                            IF c REGEXP '[[:alnum:]]' THEN 
                                SET ret=CONCAT(ret,c); 
                            END IF; 
                            SET i = i + 1; 
                            END; 
                        UNTIL i > len END REPEAT; 
                        RETURN ret; 
                    END;
                    ";

            DB::unprepared($sql);

            //check if the same listing exists
            $unit = preg_replace("/[^A-Za-z0-9 ]/", '', $request->apartment);
            //echo $unit; exit;
            //echo $request->id; exit;
            if ($request->address)
                $same_lists = DB::table('estate_data')->where('user_id', Auth::user()->id)->where('id', '<>', $request->id)->where('building_id', trim($request->address))->where(DB::raw('alphanum(unit)'), trim($unit))->get()->first();
            else
                $same_lists = DB::table('estate_data')->where('user_id', Auth::user()->id)->where('id', '<>', $request->id)->where('name', trim($request->street_address))->where(DB::raw('alphanum(unit)'), trim($unit))->where('city', trim($request->city))->get()->first();
            
            //print_r($same_lists); exit;

            if(count($same_lists)) {
                return redirect('/home/listing/rental')->withInput()->with('status', '<span style="color:red;">Error: You already created the same listing before.</span>');
            }

            if (Auth::user()->isAgent()) {
                $external_id = Auth::user()->agentInfos()->external_id;
                if ($external_id && $request->address) {
                    $same_lists = DB::table('estate_data')->join('agents_listings', 'listings_id', '=', 'estate_data.id')->where('agents_listings.agent_id', $external_id)->where('estate_data.id', '<>', $request->id)->where('building_id', trim($request->address))->where(DB::raw('alphanum(unit)'), trim($unit))->get()->first();
                }
                if(count($same_lists)) {
                    return redirect('/home/listing/rental')->withInput()->with('status', '<span style="color:red;">Error: You already created the same listing before.</span>');
                }
            }*/

            if (!$request->filters)
                $request->filters = array();

            $addRental = $this->estate->addRentalListing($request, $estate);

            if($addRental){

                if (Auth::user()->isAdmin()){
                    return redirect('/allrental')
                        ->with('status', 'List saved successfully!');
                }else{
                    if($request->feature == 1 ){
                        return redirect('feature?id='.$estate->id);
                    }else{
                        if (isset($request->id) && $request->id)
                            return redirect('/home/listing')->with('status', "Listing ID: $request->id is saved successfully!");
                        else
                            return redirect('/home/listing')->with('status', "Listing ID: $addRental is created successfully! Please click the \"Non-Active\" button to activate your listing.");
                    }
                }

            }else{
                return redirect('/home/listing/rental')
                    ->with('status', '<span style="color:red">Failed to save, please try again.</span>')
                    ->withInput();
            }

        }
        elseif (isset($request->id) && isset($request->openhouseonly) && isset($request->openHouse)) {
            //print_r($request->all()); exit;
            $openHouse = $this->open->saveOpenHouse($request->id, $request->openHouse);
            //print_r($openHouse); exit;

            if($openHouse){

                if (Auth::user()->isAdmin()){
                    return redirect('/allrental')
                        ->with('status', 'List saved successfully!');
                }else{
                    if($request->feature == 1 ){
                        return redirect('feature?id='.$estate->id);
                    }else{
                        return redirect('/home/listing')
                            ->with('status', "Listing ID: $request->id open house is saved successfully!");
                    }
                }

            }else{
                return redirect('/home/listing')
                    ->with('status', '<span style="color:red">Failed to save open house, please try again.</span>')
                    ->withInput();
            }
        }
        else {
            return redirect('/home/listing');
        }
    }

    public function submit(Request $request)
    {
        $submit = $this->estate->submitListing($request);

        if (Auth::user()->isAdmin()){
            return redirect('/allrental')->with('status', $submit);
        }else{
            return redirect('/home/listing')->with('status', $submit);
        }
    }

    public function edit(Request $request)
    {
        $request->user()->authorizeRoles(['Owner', 'Agent', 'Admin', 'man']);

        if(!empty($request->id) && !empty($request->submit)){

            if(strtolower($request->submit) === 'edit'){

                $list = $this->estate->editListing($request);

                $data = $this->filter->dataFilters();

                $openHours = $this->open->getHours();

                return view('rental', $data)->with('list', $list)->with('openHours',$openHours);
            }
        }
    }

    public function delete(Request $request)
    {
        $request->user()->authorizeRoles(['Owner', 'Agent', 'Admin', 'man']);

        if(strtolower($request->submit) == 'delete' && isset($request->id)){

            $list = $this->estate->deleteListing($request->id);

            if($list){
                if (Auth::user()->isAdmin()){
                    return redirect('/allrental')->with('status', "Listing ID: $request->id is deleted.");
                }else{
                    return redirect('/home/listing')->with('status', "Listing ID: $request->id is deleted.");
                }
            }else{
                if (Auth::user()->isAdmin()){
                    return redirect('/allrental')->with('status', 'Failed to delete listing, please try again.');
                }else{
                    return redirect('/home/listing')->with('status', 'Failed to delete listing, please try again.');
                }
            }
        }
    }

    public function deleteImage(Request $request)
    {
        $request->user()->authorizeRoles(['Owner', 'Agent', 'Admin', 'man']);

        if($this->estate->deleteImages($request)){
            return 'Image deleted';
        }else{
            return 'Failed to delete image, please try again.';
        }
    }
}
