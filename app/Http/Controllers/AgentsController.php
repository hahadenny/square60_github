<?php

namespace App\Http\Controllers;

use App\Repositories\AgentRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AgentsController extends Controller
{
    private $agent;

    public function __construct(AgentRepo $agent)
    {
        $this->agent = $agent;
    }

    public function unsubscribeEmail(Request $request) {
        if (isset($request->s) && $request->s) {
            $data = $request->s;
            $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

            $email = $this->my_decrypt($data, $key);

            DB::table('agent_infos')->where('email', $email)->update(['forward_email' => '0']);

            //echo $email; exit;

            return view('unsubscribed',  compact('data'));
        }
        elseif (isset($request->e) && $request->e) {
            $data = $request->e;
            $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

            $email = $this->my_decrypt($data, $key);

            DB::table('agent_infos')->where('email', $email)->update(['promo_email' => '0']);

            //echo $email; exit;

            return view('unsubscribe',  compact('data'));
        }
    }

    public function subscribeEmail(Request $request) {
        if (isset($request->s) && $request->s) {
            $data = $request->s;
            $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

            $email = $this->my_decrypt($data, $key);

            DB::table('agent_infos')->where('email', $email)->update(['forward_email' => '1']);

            //echo $email; exit;

            return view('subscribed');
        }
        elseif (isset($request->e) && $request->e) {
            $data = $request->e;
            $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

            $email = $this->my_decrypt($data, $key);

            DB::table('agent_infos')->where('email', $email)->update(['promo_email' => '1']);

            //echo $email; exit;

            return view('subscribed');
        }
    }

    public function unsub(Request $request) {
        return view('unsub');
    }

    public function my_decrypt($data, $key) {
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    public function profile($name='', $agent_id = 0){
        if ($agent_id)
            $agent = $this->agent->getAgentProfile($agent_id);
        else
            $agent = $this->agent->getAgentProfileByName($name);

        if(!$agent)
            return view('404');

        if ($agent_id)
            $estates = $this->agent->getAgentEstates($agent_id);
        else
            $estates = $this->agent->getAgentEstates($agent_id, $name);

        return view('profileAgent', compact('agent', 'estates'));
    }

    public function agentsList(Request $request){

        $request->user()->authorizeRoles(['admin']);

        $agentsList = $this->agent->agentsList();

        return view('showAgents', compact('agentsList'));
    }

    public function edit(Request $request){

        $request->user()->authorizeRoles(['admin']);

        $agent = $this->agent->getAgentAll($request->id);

        //print_r($agent->user->phone); exit;

        $photo = $this->agent->getAgentPhoto($agent);

        return view('editAgents', compact('agent','photo'));
    }

    public function update(Request $request){

        $request->user()->authorizeRoles(['admin']);

        /*$validator = Validator::make($request->all(), [
            //'lastName' => 'required|min:1',
            'firstName' => 'required|min:1',
            //'company' => 'required|min:1',
            //'webLink' => 'required|min:3',
            //'officePhone' => 'required'
        ]);

        if ($validator->fails())
        {
            return redirect('/agentedit')
                ->withErrors($validator)
                ->withInput();

        }*/

        if ($this->agent->updateAgent($request)){
            return redirect('/agents')->with('status', 'Agent info updated successfully.');
        }else{
            return redirect('/agents')->with('status', 'Failed to update, please try again.');
        }

    }

    public function delete(Request $request){

        $agent = $this->agent->deleteAgent($request); //todo delete photo from storage when all be in one storage

        if ($agent){
            return redirect('/agents')->with('status', 'Agent deleted');
        }else{
            return redirect('/agents')->with('status', 'Can not delete, try again');
        }
    }

    public function deleteImage(Request $request){

        if($this->agent->deleteAgentImages($request)){
            return 'Image delete';
        }else{
            return 'Can not delete, try again';
        }
    }
}
