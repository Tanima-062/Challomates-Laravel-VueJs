<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use App\Rules\MatchSamePassword;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Rules\CheckMobileAppUserSamePassword;
use App\Notifications\MobileAppUserForgotPasswordNotification;

class ForgotPasswordController extends Controller
{

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email'  =>  ['required']
        ]);


        if($user = MobileAppUser::active()->where('email', $request->email)->first()){
            DB::table('mobile_password_resets')->where('mobile_app_user_id', $user->id)->delete();

            $token = mt_rand(100000,999999);
            DB::table('mobile_password_resets')
                ->insert([
                    'mobile_app_user_id'    =>  $user->id,
                    'username'  =>  $request->email,
                    'token'     =>   $token,
                    'created_at'    =>  now(),
                    'updated_at'    =>  now(),
                ])
            ;

            // $user->notify(new MobileAppUserPasswordResetNotification($request->email, $token));
            $user->notify(new MobileAppUserForgotPasswordNotification($request->email, $token));

            return response(['message'=>'Thank you very much. The email was sent successfully.', 'status' => true]);
        }

        return response(['message'=>'User not found.', 'status' => false, 404]);
    }


    /**
     * Verify forgot pasword OTP
     *
     * @param Request $request
     * @return void
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'     =>  ['required', 'email',],
            'otp'       =>  ['required']
        ]);

        $token = DB::table('mobile_password_resets')
                ->where('token', $request->otp)
                ->first()
                ;

        if(!$token){
            return response(['status' => false], 200);
        }

        return response(['status' => true], 200);
    }

    // public function resendOtp(Request $request)
    // {
    //     $request->validate([
    //         'email'  =>  ['required']
    //     ]);


    //     if($user = MobileAppUser::where('email', $request->email)->first()){
    //         DB::table('mobile_password_resets')->where('mobile_app_user_id', $user->id)->delete();

    //         $token = mt_rand(100000,999999);
    //         DB::table('mobile_password_resets')
    //             ->insert([
    //                 'mobile_app_user_id'    =>  $user->id,
    //                 'username'  =>  $request->email,
    //                 'token'     =>   $token,
    //                 'created_at'    =>  now(),
    //                 'updated_at'    =>  now(),
    //             ])
    //         ;

    //         $user->notify(new MobileAppUserPasswordResetNotification($request->email, $token));

    //         return response(['message'=>'Thank you very much. The email was sent successfully.', 'status' => true]);
    //     }

    //     return response(['message'=>'User not found.', 'status' => false, 404]);
    // }



    /**
     * Update password by forgot password
     *
     * @param Request $request
     * @return void
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'otp'         =>  ['required'],
            'email'      =>  ['required',],
            'password'      =>  ['required', Password::min(8)->mixedCase()->numbers()->symbols(), new CheckMobileAppUserSamePassword(),],
            'confirm_password'      =>  ['required','same:password'],
        ]);
        if($user_password_reset = DB::table('mobile_password_resets')->where(['token'=> $request->otp, 'username'=>$request->email])->first()) {
            $user = MobileAppUser::where('id', $user_password_reset->mobile_app_user_id)->first();

            $user->update(['password' => bcrypt($request->password)]);

            return response(['message'=> 'Password update has been successfully', 'data'=>$user, 'status' => true], 200);
        }

        return response(['message'=> 'Invalid otp', 'status' => false], 404);
    }


}
