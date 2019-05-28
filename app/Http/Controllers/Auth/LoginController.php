<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Socialite;
use App\User;

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
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {

            //Set timezone as session
            $timezone = Auth::user()->timezone;
            session(['timezone' => $timezone]);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Redirect the user to the provider authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    /**
     * Obtain the user information from provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($driver)
    {
        try {
            $user = Socialite::driver($driver)->user();
        } catch (\Exception $e) {
            return redirect()->route('login');
        }
        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {

            // update the avatar and provider that might have changed
            $existingUser->update([
                'avatar' => $user->avatar,
                'provider' => $driver,
                'provider_id' => $user->id,
                'access_token' => $user->token
            ]);

            auth()->login($existingUser, true);
        } 
        else{
            return redirect()->route('login')->with('error','Account Doesnot Exists, Please Contact your Employer');
        }
        // else {
        //     $newUser                    = new User;
        //     $newUser->provider_name     = $driver;
        //     $newUser->provider_id       = $user->getId();
        //     $newUser->name              = $user->getName();
        //     $newUser->email             = $user->getEmail();
        //     $newUser->email_verified_at = now();
        //     $newUser->avatar            = $user->getAvatar();
        //     $newUser->save();

        //     auth()->login($newUser, true);
        // }

        return redirect($this->redirectPath());
    }
}
