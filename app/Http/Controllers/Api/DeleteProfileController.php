<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Notifications\MobileAppUser\DeleteAccountRequestNotification;
use App\Traits\UploadAble;

class DeleteProfileController extends Controller
{
    use UploadAble;


    /**
     * Sent mobile app user delete account OTP
     *
     * @param Request $request
     * @return void
     */
    public function deleteRequest(Request $request)
    {
        //Process delete request

        $user = $request->user();

        DB::table('mobile_user_deletes')->where('mobile_app_user_id', $user->id)->delete();

        $token = mt_rand(100000, 999999);

        DB::table('mobile_user_deletes')
            ->insert([
                'mobile_app_user_id'    =>  $user->id,
                'token'     =>   $token,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ])
        ;

        $user->notify(new DeleteAccountRequestNotification($token));

        return response(['message' => 'Thank you very much. The email was sent successfully.', 'status' => true]);
    }




    /**
     * Verify mobile app user account delete OTP
     *
     * @param Request $request
     * @return void
     */
    public function verifyOtp(Request $request)
    {
        //Verify delete request otp
        $request->validate([
            'otp'       =>  ['required']
        ]);

        $user = $request->user();

        $deleteRequest = DB::table('mobile_user_deletes')
            ->where('mobile_app_user_id', $user->id)
            ->where('token', $request->otp)
            ->first();

        if (!$deleteRequest) {
            return response(['status' => false], 200);
        }

        $this->deleteOne($user->photo);
        $user->forceDelete();

        return response([
            'status' => true,
            'message' => 'Your account delete successfully'
        ], 200);

        // return response(['status' => true], 200);
    }




    /**
     * Resend Mobile app user account delete OTP
     *
     * @param Request $request
     * @return void
     */
    public function resendOtp(Request $request)
    {
        $user = $request->user();

        DB::table('mobile_user_deletes')->where('mobile_app_user_id', $user->id)->delete();

        $token = mt_rand(100000, 999999);

        DB::table('mobile_user_deletes')
            ->insert([
                'mobile_app_user_id'    =>  $user->id,
                'token'     =>   $token,
                'created_at'    =>  now(),
                'updated_at'    =>  now(),
            ])
        ;

        $user->notify(new DeleteAccountRequestNotification($token));

        return response(['message' => 'Thank you very much. The email was sent successfully.', 'status' => true]);
    }




    // /**
    //  * Delete Mobile app user profile data
    //  *
    //  * @param Request $request
    //  * @return void
    //  */
    // public function deleteAccount(Request $request)
    // {
    //     $user = $request->user();

    //     $this->deleteOne($user->photo);
    //     $user->forceDelete();

    //     return response([
    //         'status' => true,
    //         'message' => 'Your account delete successfully'
    //     ], 200);
    // }
}
