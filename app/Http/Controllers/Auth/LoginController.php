<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    { 
        return view('auth.login'); 
    }

    /**
     * Override the login method to allow login with email or phone
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Override the attemptLogin method to check username, email, and phone
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $input = $credentials['email'];
        $password = $credentials['password'];
        $remember = $request->filled('remember');
        $fields = $this->getPossibleFields($input);

        foreach ($fields as $field) {
            if ($this->guard()->attempt([
                $field => $input,
                'password' => $password,
            ], $remember)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of possible fields based on the input format, ordered by priority.
     * - email, identity_num (10 digits), phone, username
     */
    protected function getPossibleFields($input)
    {
        $fields = [
            'email',
            'identity_num',
            'phone',
            'username',
        ]; 

        return array_unique($fields);
    }

    /**
     * Override the credentials method to handle username, email, and phone
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'password', 'identity_num');
    }

    /**
     * Override the validateLogin method to update validation rules
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Override the sendFailedLoginResponse method to provide better error messages
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
}
