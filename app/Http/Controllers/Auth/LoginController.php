<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

    }

    public function showLoginForm(Request $request)
    {  
        if (isset($request->_s) && $request->_s) {
            $key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';
            $secret = $this->my_decrypt($request->_s, $key);
            
            if (preg_match('/|||||/', $secret)) {
                $data = explode('|||||', $secret);
                $name = $data[0];
                $email = $data[1];
                $pwd = trim($data[2]);
                //echo $pwd;
                $hash_pwd = password_hash($pwd, PASSWORD_DEFAULT);

                $agent_info = DB::table('agent_infos')->where('email', $email)->where('user_id', 0)->get()->first();

                if (count($agent_info)) {
                    DB::table('users')->insert(
                        ['name' => $name, 'email' => $email, 'password' => $hash_pwd, 'created_at' => Carbon::now()]
                    );

                    $user_id = DB::getPdo()->lastInsertId();

                    DB::table('agent_infos')->where('email', $email)->update(['user_id' => $user_id]);

                    DB::table('role_user')->insert(
                        ['role_id' => 3, 'user_id' => $user_id]
                    );

                    session(['email' => $email, 'pwd' => $pwd]);
                }
            }
        }

        session(['link' => url()->previous()]);
        return view('auth.login');
    }

    public function my_decrypt($data, $key) {
        // Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    protected function authenticated(Request $request, $user)
    { 
        //update ipaddr
        $ipaddr = isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

        $country_city = '';
        $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ipaddr"));
        if ($geo && isset($geo['geoplugin_status']) && $geo['geoplugin_status'] == '200') {
            $country = isset($geo["geoplugin_countryName"]) ? $geo["geoplugin_countryName"] : '';
            $city = isset($geo["geoplugin_city"]) && $geo["geoplugin_city"] ? $geo["geoplugin_city"] : isset($geo['geoplugin_region']) && $geo['geoplugin_region'] ? $geo['geoplugin_region'] : '';
            if ($country) {
                $country_city = $country;
                if ($city)
                    $country_city .= "/$city";
            }
        }

        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['ipaddr' => $ipaddr, 'location' => $country_city]);

        setcookie(
            "login_email",
            $request->email,
            time() + (10 * 365 * 24 * 60 * 60)
            );

        if(Auth::user()->isAdmin()){
            return redirect('/agents');
            //return redirect(session('link'));
        }elseif(Auth::user()->isAgent()){
            if (session('email'))
                return redirect('/home');
            else
                return redirect('/home/profile');
        }elseif (Auth::user()->isOwner()){
            return redirect('/home/profile/owner');
        }elseif (Auth::user()->isMan()){
            return redirect('/home/profile/man');
        }else {
            return redirect($this->redirectTo);
        }

    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

}
