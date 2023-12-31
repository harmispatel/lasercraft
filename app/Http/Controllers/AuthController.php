<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show Login Form
     */
    public function showLogin()
    {
        return view('auth.login');
    }


    /**
     * Show Register Form
     */
    public function showRegister()
    {
        return view('auth.register');
    }


    /**
     * Register the User
     */
    public function register(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        $user_verify_token = genratetoken(8);
        $input = $request->except(['_token','password','confirm_password']);
        $input['password'] = Hash::make($request->password);
        $input['user_type'] = 3;
        $input['user_verify'] = 0;
        $input['status'] = 1;
        $input['verify_token'] = $user_verify_token;

        try {

            $user = $this->create($input);

            if($user){

                $to_email = $user->email;
                $details['firstname'] = $user->firstname;
                $details['lastname'] = $user->lastname;
                $details['user_token'] = $user_verify_token;
                $details['verification_link'] = route('customer.verify',encrypt($user->id));

                \Mail::to($to_email)->send(new \App\Mail\VerifyUser($details));

                // Auth::login($user);
                // $username = $user['firstname'] ." ". $user["lastname"];
                // return redirect()->route('home')->with('success','Hello, '. $username);

                return redirect()->route('customer.verify',encrypt($user->id))->with('success','Your Account has been Registerd SuccessFully, Please Verify Your Account to Access It.');

            }else{
                return redirect()->back()->with('error','Something Went Wrong!');
            }

        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Something Went Wrong!');
        }

    }


    public function create(array $input)
    {
        return User::create($input);
    }



    /**
     * Authenticate the User
     *
     * @param Request $request
     */
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $input = $request->except('_token');

        if (Auth::attempt($input))
        {
            $user_verify = (isset(Auth::user()->user_verify) && Auth::user()->user_verify == 1) ? Auth::user()->user_verify : 0;
            $user_status = (isset(Auth::user()->status) && Auth::user()->status == 1) ? Auth::user()->status : 0;
            $user_id = (isset(Auth::user()->id)) ? Auth::user()->id : '';

            if(Auth::user()->user_type == 1)
            {
                $user_status = (isset(Auth::user()->status) && Auth::user()->status == 1) ? Auth::user()->status : 0;

                if($user_verify == 0 || $user_status == 0)
                {
                    Auth::logout();
                    return redirect()->route('login')->with('error','Please Verify Your Account to Access Login');
                }

                $username = Auth::user()->firstname." ".Auth::user()->lastname;
                return redirect()->route('client.dashboard')->with('success', 'Welcome '.$username);
            }
            elseif(Auth::user()->user_type == 3){

                if($user_verify == 0)
                {
                    Auth::logout();
                    return redirect()->route('customer.verify',encrypt($user_id))->with('error','Please Verify Your Account to Access Login');
                }

                if($user_status == 0)
                {
                    Auth::logout();
                    return redirect()->route('login')->with('error','Your account has been Blocked. Contact Admin to Unblock It.');
                }

                $username = Auth::user()->firstname." ".Auth::user()->lastname;
                return redirect()->route('home')->with('success','Hello, '. $username);
            }
        }
        return back()->with('error', 'Please Enter Valid Email & Password');
    }


    /**
     * Logout the User
     */
    public function logout()
    {
        if(Auth::user()->user_type == 3){
            \Cart::clear();
            Auth::logout();
            return redirect()->route('home');
        }else{
            Auth::logout();
            return redirect()->route('login');
        }
    }
}
