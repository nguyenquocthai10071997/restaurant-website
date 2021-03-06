<?php

namespace App\Http\Controllers\AdminAuth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use App\Admin;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
    	return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
    	$user = Socialite::driver('google')->user();

    	$data = array(
    		'email' => $user->email,
    		'name' => $user->name,
    		'avatar' => $user->avatar,
    		'google_id' => $user->id,
    	);

        // dd($user);

    	$mail_name = explode('@', $user->email)[0];

    	$data['password'] = md5($mail_name);

    	$exist = Admin::where('email','=',$user->email)->first();

    	if (!$exist) {
    		
    		return redirect()->route('admin.login');

    	} else {
    		$user_log = Admin::firstOrCreate(['email' => $user->email], $data);

    		Auth::guard('admin')->loginUsingId($user_log->id);

    		return redirect()->route('admin.home');
    	}

    }
}
