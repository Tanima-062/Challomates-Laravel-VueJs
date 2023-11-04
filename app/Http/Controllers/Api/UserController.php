<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\Rules\Password;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\MobileAppUser\VerifyEmail;

use App\Http\Requests\MobileAppUser\UpdateProfileRequest;
use App\Http\Resources\MobileAppUser\MobileAppUserResource;
use App\Notifications\MobileAppUser\MobileAppUserPasswordResetNotification;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use UploadAble;

    /**
     * Get current authenticated user data
     *
     * @param Request $request
     * @return void
     */
    public function me(Request $request)
    {
        // return (new MobileAppUserResource($request->user()))->additional([
        //     'status'    =>  true
        // ]);

        return response([
            'data' => [
                'user' => new MobileAppUserResource($request->user()),

            ],
        ]);
    }




    /**
     * Update profile information
     *
     * @param Request $request
     * @return void
     */
    public function update(UpdateProfileRequest $request)
    {

        $user = $request->user();
        $data = $request->only(['username', 'first_name', 'last_name', 'date_of_birth', 'street', 'house_number', 'zip_code', 'city', 'country', 'country_iso_code', 'privacy', 'language_id', 'phone_number']);

        if ($request->email !== $user->email) {

            $token = mt_rand(100000, 999999);
            EmailVerification::where('mobile_app_user_id', $user->id)->delete();
            $payload = EmailVerification::create([
                'email' => $request->email,
                'token' => $token,
                'mobile_app_user_id' => $user->id,
            ]);

            $payload->notify(new VerifyEmail($token, $user));
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadOne($request->photo, 'mobile_app_users', env('FILESYSTEM_DISK', 'public'));

            $this->deleteOne($user->photo, env('FILESYSTEM_DISK', 'public'));
        }

        $user->update($data);

        return response((new MobileAppUserResource($user->fresh()))->additional(['status' => true]), Response::HTTP_ACCEPTED);
    }




    /**
     * Send mobile app user reset password link
     *
     * @param Request $request
     * @return void
     */
    public function resetPassword(Request $request)
    {
        $user = $request->user();

        DB::table('mobile_password_resets')->where('mobile_app_user_id', $user->id)->delete();

        $token = mt_rand(100000, 999999);
        DB::table('mobile_password_resets')
            ->insert([
                'mobile_app_user_id'    =>  $user->id,
                'username'  =>  $user->email,
                'token'     =>   $token,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ]);

        $user->notify(new MobileAppUserPasswordResetNotification($user->email, $token));

        return response(['message' => 'Thank you very much. The email was sent successfully.', 'status' => true]);
    }


    public function isLogin(Request $request)
    {
        if (!$request->token) {
            return response(['status' => false]);
        }

        $token = PersonalAccessToken::findToken($request->token);

        if ($token) {
            return response(['status' => true]);
        }

        return response(['status' => false]);
    }

    /**
     * Verify User OTP
     *
     * @param Request $request
     * @return void
     */
    public function verifyResendPasswordOtp(Request $request)
    {
        $request->validate([
            'otp'       =>  ['required']
        ]);

        $user = $request->user();

        $passwordReset = DB::table('mobile_password_resets')
            ->where('mobile_app_user_id', $user->id)
            ->where('token', $request->otp)
            ->first();

        if (!$passwordReset) {
            return response(['status' => false], 200);
        }

        return response(['status' => true], 200);
    }

    /**
     * Resend Registration OTP
     *
     * @param Request $request
     * @return void
     */
    public function resendResendPasswordOtp(Request $request)
    {
        $user = $request->user();

        DB::table('mobile_password_resets')->where('mobile_app_user_id', $user->id)->delete();

        $token = mt_rand(100000, 999999);
        DB::table('mobile_password_resets')
            ->insert([
                'mobile_app_user_id'    =>  $user->id,
                'username'  =>  $user->email,
                'token'     =>   $token,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ]);

        $user->notify(new MobileAppUserPasswordResetNotification($user->email, $token));

        return response(['message' => 'Thank you very much. The email was sent successfully.', 'status' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', 'same:password']
        ]);

        $user = $request->user();
        if (Hash::check($request->password, $user->password)) {
            // return ValidationException::withMessages([
            //     'password_previous' =>  'No previously used password'
            // ]);
            throw ValidationException::withMessages([
                'password_previous' =>  'No previously used password'
            ]);
        }

        $user = $request->user();

        $user->update([
            'password'  =>  bcrypt($request->password),
            // 'verification_token'  =>  null,
            // 'status' => MobileAppUser::STATUS_ACTIVE,
        ]);

        return response([
            // 'data'  =>  new MobileAppUserResource($user->fresh()),
            'status'    =>  true,
        ], Response::HTTP_OK);
    }

    public function resendEmailVerificationToken(Request $request)
    {
        $request->validate([
            'email' =>  ['required', 'email:filter'],
        ]);

        $user = $request->user();
        $payload = EmailVerification::where('mobile_app_user_id', $user->id)->first();
        if (!$payload) {
            $token = mt_rand(100000, 999999);
            DB::table('mobile_email_verification')->insert([
                'email' => $request->email,
                'token' => $token,
                'mobile_app_user_id' => $user->id,
            ]);

            $payload->notify(new VerifyEmail($token, $user));
            return response(['status' => true], 200);
        }

        $payload->notify(new VerifyEmail($payload->token, $user));
        return response(['status' => true], 200);
    }

    public function verifyEmail(Request $request, $token)
    {
        $user = $request->user();

        $payload = EmailVerification::where([['token', '=', $token], ['mobile_app_user_id', '=', $user->id]])->first();

        if (!$payload) {
            return response(['status' => false], 200);
        }

        $user->update(['email' => $payload->email]);
        EmailVerification::where([['token', '=', $token], ['mobile_app_user_id', '=', $user->id]])->delete();
        return response(['status' => true], 200);
    }

    public function blockUser(MobileAppUser $user)
    {
       Auth::user()->blockedMobileAppUsers()->syncWithoutDetaching($user);
       return response(['status' => true], 200);
    }
}
