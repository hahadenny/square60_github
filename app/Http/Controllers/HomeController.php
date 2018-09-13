<?php

namespace App\Http\Controllers;

use App\Repositories\OpenHouseRepo;
use App\Repositories\EstateInfoRepo;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private $user;
    private $estate;
    private $open;

    public function __construct(UserRepo $user, EstateInfoRepo $estateInfoRepo, OpenHouseRepo $openRepo)
    {
        $this->middleware('auth');
        $this->user = $user;
        $this->estate = $estateInfoRepo;
        $this->open = $openRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function listing(Request $request){

        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        $sellListing = $this->estate->showListing(Auth::user()->id, 1);

        $rentalListing = $this->estate->showListing(Auth::user()->id, 2);

        $openHours = $this->open->getHours();

        return view('listing',compact('sellListing','rentalListing'))->with('openHours', $openHours);
    }

    public function ownerMail(Request $request) {
        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        //if (!Auth::user()->premium)
            //return view('404');

        $buildings = $this->getBuildings();
        //print_r($buildings[0]); exit;

        return view('ownermail')->with('buildings',$buildings);
    }

    public function generateList(Request $request) {
        $request->user()->authorizeRoles(['Owner', 'Agent', 'man']);

        //if (!Auth::user()->premium)
            //return view('404');

        //print_r($request->all());exit;
        $free = false;

        if (Auth::user()->premium == 3) 
            $max = env('DIAM_MAIL');
        elseif (Auth::user()->premium == 1) 
            $max = env('GOLD_MAIL');
        elseif (Auth::user()->premium == 1) 
            $max = env('SILV_MAIL');
        else {
            $max = env('FREE_MAIL');
            $free = true;
        }

        //if ($free && isset($_COOKIE['free_mail']) && $_COOKIE['free_mail'] >= $max)
            //return redirect('ownermail')->withInput()->with('status', "<span style='color:red'>Sorry, you can only download $max records maximum for free. If you want to download more, please <a href='/upgrade' style='color:red;text-decoration:underline;'>upgrade your membership</a>.</span>");
        //else
        //echo Auth::user()->mail_count; exit;
        if (Auth::user()->mail_count >= $max) {
            if ($free)
                return redirect('ownermail')->withInput()->with('status', "<span style='color:red'>Sorry, you can only download $max records maximum for free. If you want to download more, please <a href='/upgrade' style='color:red;text-decoration:underline;'>upgrade your membership</a>.</span>");
            else
                return redirect('ownermail')->withInput()->with('status', "<span style='color:red'>Sorry, you can only download $max records maximum per month.</span>");
        }

        $builds = $request->build;
        //print_r($builds); exit;
        $bbls = array();
        $total_units = 0;

        foreach ($builds as $build) {
            if ($build['bbl'] && !in_array($build['bbl'], $bbls)) {
                //echo $build['bbl'];
                $bbls[] = $build['bbl'];
                $total_units += ($build['units']-1);  //-1 for building itself
                //exit;
            }
        }

        if (!$total_units)
            return redirect('ownermail')->withInput()->with('status', "<span style='color:red'>Please select at least 1 condo building.</span>");

        $mail_count = Auth::user()->mail_count + $total_units;

        //if (Auth::user()->mail_count > $max) {
        //if ($free && isset($_COOKIE['free_mail']) && ($_COOKIE['free_mail'] + $total_units) > $max)
            //return redirect('ownermail')->withInput()->with('status', "<span style='color:red'>Sorry, you can only download $max records maximum for free. If you want to download more, please <a href='/upgrade' style='color:red;text-decoration:underline;'>upgrade your membership</a>.</span>");
        //else
        if ($mail_count > $max) {
            $mail_left = $max - Auth::user()->mail_count;
            if ($free)
                return redirect('ownermail')->withInput()->with('status', "<span style='color:red'>Sorry, this building has $total_units units. You have $mail_left free records left. You can only download $max records maximum for free. If you want to download more, please <a href='/upgrade' style='color:red;text-decoration:underline;'>upgrade your membership</a>.</span>");
            else
                return redirect('ownermail')->withInput()->with('status', "<span style='color:red'>Sorry, this building has $total_units units. You have $mail_left free records left. You can only download $max records maximum per month.</span>");
        }

        //Download csv on the fly
        $ts = date('Y-m-d');
        $fname = "mailing_list_$ts.csv";
        $records = array();
        $records[] = array('BBL', 'Block', 'Lot', 'Condo ID', 'House Number Low', 'House Number High', 'Street', 'Apartment Number', 'Building Address', 'City', 'State', 'Zip Code', 'Owner');

        //print_r($bbls); exit;

        $rs = DB::connection('mysql2')->table('units')->whereIn('building_bbl', $bbls)->where('lot', 'not like', '75%')->get();
        foreach($rs as $r) {
            $arr = get_object_vars($r);
            unset($arr['id']);
            unset($arr['building_bbl']);
            $records[] = $arr;
        }

        $this->array_to_csv_download($records, $fname);

        DB::table('users')->where('id', Auth::user()->id)->update(['mail_count' => $mail_count]);

        if ($free) {
            if (!isset($_COOKIE['free_mail'])) {
                setcookie(
                    "free_mail",
                    $mail_count,
                    time() + (10 * 365 * 24 * 60 * 60)
                );
            }
            else {
                setcookie(
                    "free_mail",
                    $mail_count + $_COOKIE['free_mail'],
                    time() + (10 * 365 * 24 * 60 * 60)
                );
            }
        }

        //echo $total_units;
    }

    public function array_to_csv_download($array, $filename = "mailing_list.csv", $delimiter=",") {
        // open raw memory as file so no temp files needed, you might run out of memory though
        $f = fopen('php://memory', 'w'); 
        // loop over the input array
        foreach ($array as $line) { 
            // generate csv lines from the inner arrays
            fputcsv($f, $line, $delimiter); 
        }
        // reset the file pointer to the start of the file
        fseek($f, 0);
        // tell the browser it's going to be a csv file
        header('Content-Type: application/csv');
        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        // make php send the generated csv lines to the browser
        fpassthru($f);
    }

    public function getBuildings() {
        $buildings = DB::connection('mysql2')->table('buildings')->orderBy('address', 'ASC')->get();
        //print_r($buildings); exit;

        return $buildings;
    }

    public function profileAgent(Request $request){

        $request->user()->authorizeRoles(['Agent']);

        $agentInfo = $this->user->userAgent($request->user()->id);

        $alreadyExpert = $agentInfo->subscribed('expert');

        $sellListing = $this->estate->showListing(Auth::user()->id, 1);

        $rentalListing = $this->estate->showListing(Auth::user()->id, 2);

        $openHours = $this->open->getHours();

       if($alreadyExpert){
           return view('profileExpert', compact('sellListing','rentalListing'))->with('agent', $agentInfo->userAgent)->with('openHours', $openHours);
       }else{
           return view('profile', compact('sellListing','rentalListing'))->with('agent', $agentInfo->userAgent)->with('openHours', $openHours);
       }
    }

    public function editProfileAgent(Request $request){
        $post = $request->all();

        //$request->officePhone = $post['officePhone'] = preg_replace("/[^0-9]/", "", $post['officePhone']);
        $request->cellPhone = $post['cellPhone'] = preg_replace("/[^0-9]/", "", $post['cellPhone']);

        //print_r($request->all()); exit;

        $validator = Validator::make($post, [
            'lastName' => 'required|min:1',
            'firstName' => 'required|min:1',
            'photo' => 'image|max:20480',
            'email' => 'required|email',
            'company' => 'required|min:1',
            'logo' => 'image|max:20480',
            //'webLink' => 'required|min:3',
            //'weblink' => 'min:3',
            //'officePhone' => 'required|numeric',
            'officePhone' => 'required',
            //'cellPhone' => 'required|numeric',
            //'fax' => 'required|numeric',
            //'fax' => 'numeric',
            //'description' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return redirect('/home/profile')
                ->withErrors($validator)
                ->withInput();
        }

        if($this->user->updateAgentProfile($request)){

            $request->session()->flash('status', 'Your profile is updated successfully!');

            return redirect('/home/profile');
        }else{

            $request->session()->flash('status', 'Error, pleae try again later');

            return redirect('/home/profile');
        }
    }

    public function editProfile(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'numeric',
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if ($request->changepwd) {
            $rules['currentPassword'] = 'required_with:password';
            $rules['password'] = 'required|string|min:6|confirmed';

            session(['pwd' => '']);
        }

        if (isset($request->forward_email)) {
            DB::table('agent_infos')->where('user_id', $request->id)->update(['forward_email' => $request->forward_email]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect('/home')
                ->withErrors($validator)
                ->withInput();
        }

        $email = DB::table('users')->where('email', $request->email)->where('id', '<>', $request->id)->get();

        if (count($email)) {
            $request->session()->flash('error', 'This email is already registered.');
            return redirect()->route('home');
        }
        elseif ($this->user->updateProfile($request)) {

            $request->session()->flash('status', 'Settings updated.');

            return redirect()->route('home');

        } else {
            $request->session()->flash('error', 'Current password does not match.');

            return redirect()->route('home');
        }
    }

    public function profileOwner(Request $request){

        $request->user()->authorizeRoles(['Owner']);

        $sellListing = $this->estate->showListing(Auth::user()->id, 1);

        $rentalListing = $this->estate->showListing(Auth::user()->id, 2);

        $openHours = $this->open->getHours();

        return view('profileOwner',compact('sellListing','rentalListing'))->with('openHours', $openHours);
    }

    public function editProfileOwner(Request $request){

        $request->user()->authorizeRoles(['Owner']);

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|min:3',
            'email' => 'required|email',
            'cellPhone' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect('/home/profile/owner')
                ->withErrors($validator)
                ->withInput();
        }

        if($this->user->updateOwnerProfile($request)){

            $request->session()->flash('status', 'Your profile is updated successfully!');

            return redirect('/home/profile/owner');
        }else{

            $request->session()->flash('status', 'Error, try again late');

            return redirect('/home/profile/owner');
        }
    }

    public function profileMan(Request $request){

        $request->user()->authorizeRoles(['man']);

        $sellListing = $this->estate->showListing(Auth::user()->id, 1);

        $rentalListing = $this->estate->showListing(Auth::user()->id, 2);

        $openHours = $this->open->getHours();

        return view('profileMan',compact('sellListing','rentalListing'))->with('openHours', $openHours);
    }

    public function editProfileMan(Request $request){

        $request->user()->authorizeRoles(['man']);

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|min:3',
            'email' => 'required|email',
            'cellPhone' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect('/home/profile/man')
                ->withErrors($validator)
                ->withInput();
        }

        if($this->user->updateOwnerProfile($request)){

            $request->session()->flash('status', 'Your profile is updated successfully!');

            return redirect('/home/profile/man');
        }else{

            $request->session()->flash('status', 'Error, try again late');

            return redirect('/home/profile/man');
        }
    }

    public function uploadWatermarkImages($images,$userId,$logo){

        $watermark = $logo;

        foreach ($images as $k=>$item){

            $img = Image::make($item);

            $resizePercentage = 70;//70% less then an actual image (play with this value)

            $watermarkSize = round($img->width() * ((100 - $resizePercentage) / 100), 2); //watermark will be $resizePercentage less then the actual width of the image

            // resize watermark width keep height auto
            $watermark->resize($watermarkSize, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            //insert resized watermark to image center aligned
            $img->insert($watermark, 'bottom-right', 10, 10)->encode('jpg');

            $imageFileName = uniqid(time()). '.jpg';

            $filePath = 'images-rental/unit-images/'.$userId.'/' . $imageFileName;

            $s3 = Storage::disk('s3');

            $s3->put($filePath, (string)$img, 'public');

            $fileNames[] = $imageFileName;
            $filePaths[] = $filePath;
        }

        return compact('fileNames', 'filePath');
    }
}
