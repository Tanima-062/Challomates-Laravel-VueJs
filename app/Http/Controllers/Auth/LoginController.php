<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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




    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        $message = session('message') ?? null;
        session()->forget('message');

        return Inertia::render('Auth/Login', [
            'message'   =>  $message
        ]);
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credential = [
            'password'  =>  $request->password,
            'status' => User::STATUS_ACTIVE,
            'type'  =>  ['system_admin', 'challo_mates_admin', 'company_consultant']
        ];

        if ($this->username() == 'phone_number') {
            return $credential + ['user_medium'  => 'phone', 'phone_number'  =>  $request->username];
        }

        if ($this->username() == 'email') {
            return $credential + ['user_medium'  => 'email', 'email'  =>  $request->username,];
        }
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }




    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $username = filter_var(request()->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';

        return $username;
    }
}
