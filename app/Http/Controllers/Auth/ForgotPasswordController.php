<?php

namespace App\Http\Controllers\Auth;

use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;



    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return Inertia::render('Auth/ForgotPassword');
    }





    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'username'  =>  ['required']
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $credential = $this->credentials($request);


        $user = User::where( $credential )->where( 'status', User::STATUS_ACTIVE)->first();

        if (!$user) {
            return $this->sendResetLinkFailedResponse($request, "User was not found!");
        }

        PasswordReset::where($credential)->delete();

        $credential['token'] = Hash::make(uniqid() . rand(1000, 9000));
        PasswordReset::create($credential);
        $user->notify(new ResetPasswordNotification($credential['token']));
        //$user->update(['status' => User::STATUS_PENDING]);
        //return $this->sendResetLinkResponse($request, 'Password reset link has been sent!');
        return $this->sendResetLinkResponse($request, 'Vielen Dank. Die E-Mail wurde erfolgreich gesendet.');
    }

    /**
     * Send a reset link to the logged in user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkToLoggedInUser(Request $request)
    {
        $user = auth('employee')->user();
        $user->user_medium == 'email' ? $credential['email'] = $user->email : $credential['phone_number'] = $user->phone_number;

        PasswordReset::where($credential)->delete();

        $credential['token'] = Hash::make(uniqid() . rand(1000, 9000));
        PasswordReset::create($credential);

        $user->notify(new ResetPasswordNotification($credential['token']));
        $user->update(['status' => User::STATUS_PENDING]);
        auth('employee')->logout();
        //return $this->sendResetLinkResponse($request, 'Password reset link has been sent!');
        return $this->sendResetLinkResponse($request, 'Thank you very much. The email was sent successfully.');
    }


    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (filter_var(request()->username, FILTER_VALIDATE_EMAIL)) {
            $credential =  ['email' =>  $request->username];
        } else {
            $credential =  ['phone_number' => $request->username];
        }

        return $credential;
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($response)], 200)
            : back()->with('success', trans($response));
    }
    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'username' => [trans($response)],
            ]);
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => trans($response)]);
    }
}
