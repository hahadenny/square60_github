<?php
namespace App\Http\Controllers;

use App\Repositories\ShowRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ShowController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $showRepo;

    public function __construct(ShowRepo $showRepo)
    {
        $this->showRepo = $showRepo;
    }

    public function show(Request $request, $name, $unit='', $city='', $id='')
    {   
        //$result = $this->showRepo->estate->getListing($id);
        $result = $this->showRepo->estate->getListingByName($name, $unit, $city, $id);
        
        if (!$result || (!$result->active)){
            return view('404');
        }
        else
            return view('show2')->with($this->showRepo->show($request, $name, $result->id));
    }

    public function save(Request $request) {
        //print_r($request->name); exit;
        $result = $this->showRepo->saveListing($request);

        $estate = $this->showRepo->estate->getListing($request->save_id);

        if ($estate->unit)
            $listing_url = '/show/'.str_replace(' ','_',$estate->name).'/'.str_replace('#', '', str_replace(array(' ', '/'),'_',$estate->unit)).'/'.str_replace(' ','_',$estate->city);
        else
            $listing_url = '/show/'.str_replace(' ','_',$estate->name).'/_/'.str_replace(' ','_',$estate->city);
        
        if (!$result){
            return redirect($listing_url)->with('status', 'Listing already saved.');
        }
        else
            return redirect($listing_url)->with('status', 'Listing saved successfully!');
    }
}