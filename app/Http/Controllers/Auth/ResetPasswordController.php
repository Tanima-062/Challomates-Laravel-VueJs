<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        filter_var($request->username, FILTER_VALIDATE_EMAIL) ? $credential['email']  = $request->username : $credential['phone_number']  = $request->username;
        $user = User::where($credential)->first();
        if (!$user) {
            return redirect('/');
        }

        return Inertia::render('Auth/ResetPassword', ['token' => $token, 'username' => $request->username]);
    }




    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);

        $user->setRememberToken(Str::random(60));
        $user->status = User::STATUS_ACTIVE;
        $user->save();

        event(new PasswordReset($user));
        return true;
    }



    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages(), [
            'password_confirmation.same' => 'Password and confirmation do not match.',
        ]);
        filter_var($request->username, FILTER_VALIDATE_EMAIL) ? $credential['email']  = $request->username : $credential['phone_number']  = $request->username;
        $user = User::where($credential)->firstOrFail();

        $token = $user->resetToken()->where('token', $request->token)
            ->where('created_at', '>', Date::now()->subDay())
            ->first();

        if (!$user || !$token) {
            return $this->sendResetFailedResponse($request, "This password reset token is invalid.");
        }

        if (Hash::check($request->password, $user->password)) {
            $message = "Kein zuvor verwendetes Passwort";
            if($request->wantsJson() && $request->is('/api*')) {
                throw ValidationException::withMessages([
                    'password' => $message
                ]);
            }
            return back()->withErrors(['password' => $message]);
            // return ($request->wantsJson() && $request->is('/api*')) ? ValidationException::withMessages([
            //     'password' => $message
            // ]) : back()->withErrors(["password" => $message]);
        }

        $this->resetPassword($user, $request->password);
        $token->delete();

        return ($request->wantsJson() && $request->is('/api*'))
            ? new JsonResponse(['message' => trans("Vielen Dank. Ihr Passwort wurde erfolgreich zurückgesetzt. Sie können sich ab sofort mit Ihrem neuen Passwort anmelden.")], 200)
            : redirect('login')->with('success', trans("Vielen Dank. Ihr Passwort wurde erfolgreich zurückgesetzt. Sie können sich ab sofort mit Ihrem neuen Passwort anmelden."));
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'username' => ['required'],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['same:password']

        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        /*return [
            'password.required' => 'Password Lagbe',
            'password.min' => 'Password min Lagbe',
            'password.letters' => 'Password letters Lagbe',
            'password.mixed' => 'asdasdsad',
        ];*/
        return [];
    }



    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
