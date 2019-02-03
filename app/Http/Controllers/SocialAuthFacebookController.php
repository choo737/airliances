<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Auth;
use App\Services\SocialFacebookAccountService;
//use Illuminate\Support\Facades\Auth;

class SocialAuthFacebookController extends Controller
{
    //
    public function redirect($socialservice)
    {
        return Socialite::driver($socialservice)->redirect();
    }

    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    public function callback($socialservice, SocialFacebookAccountService $service)
    {
        session()->put('state', request()->input('state'));
        $user = $service->createOrGetUser(Socialite::with($socialservice)->user());
        Auth::login($user);

        return redirect()->to('/home');
    }
}
