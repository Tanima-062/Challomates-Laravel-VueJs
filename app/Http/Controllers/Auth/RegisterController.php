<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Rules\MatchSamePassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }



    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $credential = ['status' => User::STATUS_PENDING];
        filter_var($request->username, FILTER_VALIDATE_EMAIL) ? $credential['email']  = $request->username : $credential['phone_number']  = $request->username;

        if ($user = User::where($credential)->where('verification_token', $request->token)->firstOrFail()) {

            return Inertia::render('Auth/Register', [
                'username' => $request->username,
                'name'      =>  $user->name,
                'token'     =>  $user->verification_token,
                'type'      =>  $user->type,
                'reset'     =>  !!request('reset', 0)
            ]);
        }

        //return Inertia::render('Auth/Register' );

        abort(404);
    }

    /**
     * Show the application registration form.
     *
     * @return Illuminate\Support\Facades\Redirect
     */
    public function complete(Request $request)
    {

        $request->validate(
            [
                'token'      =>  ['required'],
                'username'      =>  ['required'],
                'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), new MatchSamePassword()],
                'password_confirmation' => ['required', 'same:password']
            ],
            [
                'password_confirmation.same' => 'password_confirmation_does_not_match_Password',
            ]
        );

        filter_var($request->username, FILTER_VALIDATE_EMAIL) ? $credential['email']  = $request->username : $credential['phone_number']  = $request->username;
        $user = User::where($credential)->where('verification_token', $request->token)->firstOrFail();

        $user->update([
            'password'  =>  bcrypt($request->password),
            'verification_token'  =>  null,
            'status' => User::STATUS_ACTIVE,
        ]);

        return redirect()->route('login');
    }
}
