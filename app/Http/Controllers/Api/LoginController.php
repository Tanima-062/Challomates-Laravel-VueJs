<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Resources\MobileAppUser\MobileAppUserResource;

class LoginController extends Controller
{
    use ThrottlesLogins, HasApiTokens;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $request->validate([
            'username'      =>  ['required'],
            'password'      =>  ['required'],
        ]);

        $credentials = [
            $this->username($request) => $request->username,
            'status' => MobileAppUser::STATUS_ACTIVE,
            // 'type' => 'mobile_app_user'
        ];


        if ($user =  MobileAppUser::where($credentials)->first()) {
            if (!Hash::check($request->password, $user->password)) {
                $this->incrementLoginAttempts($request);

                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $this->clearLoginAttempts($request);

            if($request->fcm_token){
                DB::table('fcm_tokens')->updateOrInsert(
                    ['mobile_app_user_id'   =>  $user->id, 'fcm_token'=>$request->fcm_token],
                    ['fcm_token'     =>  $request->fcm_token, 'created_at'=>now(),'updated_at'=>now()]
                );
            }

            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            return response()->json(
                array(
                    'data' => [
                        'user' => new MobileAppUserResource($user),
                        'token' => [
                            'access_token' => $user->createToken('authToken')->plainTextToken,
                            'token_type' => 'bearer',
                        ],
                        'fcm_tokens'    =>  $user->fcmTokens->pluck('fcm_token'),
                    ],
                    'status' => true,
                )
            );
        } else {
            $this->incrementLoginAttempts($request);
        }
        throw new AuthenticationException();
    }

    public function username()
    {
        return  filter_var(request()->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('employee');
    }
}
