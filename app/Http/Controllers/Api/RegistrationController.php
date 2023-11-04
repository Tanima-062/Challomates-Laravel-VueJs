<?php

namespace App\Http\Controllers\Api;

use App\Traits\UploadAble;
use App\Models\SalesPartner;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use App\Rules\MatchSamePassword;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\MobileAppUser\MobileAppUserResource;
use App\Http\Requests\MobileAppUser\SelfRegistrationRequest;
use App\Notifications\MobileAppUser\MobileAppUserRegistrationNotification;

class RegistrationController extends Controller
{
    use UploadAble;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SelfRegistrationRequest $request)
    {
        $data = $request->only(['username', 'email', 'first_name', 'last_name', 'date_of_birth', 'street', 'house_number', 'zip_code', 'city', 'country', 'country_iso_code', 'privacy', 'language_id', 'phone_number']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadOne($request->photo, 'mobile_app_users', env('FILESYSTEM_DISK', 'public'));
        }

        $type = MobileAppUser::DIRECT_CONSUMER;
        if($request->has('sales_partner_id') && !is_null($request->sales_partner_id) ){

            $data['sales_partner_id'] = $request->sales_partner_id;
            // $data['type'] = 'distribution_consumer';
            // $type = MobileAppUser::DISTRIBUTION_CONSUMER;

            $salesPartner = SalesPartner::find($request->sales_partner_id);

            // dd($salesPartner->currentContract->package);

            $mobile_app_users = MobileAppUser::query()
                ->where('sales_partner_id', $request->sales_partner_id)
                ->where('type', MobileAppUser::DISTRIBUTION_CONSUMER)
                ->count()
                // ->get()
                ;
            // dd($mobile_app_users);
            if($salesPartner->currentContract){
                $package = $salesPartner->currentContract->package;
                if($mobile_app_users<$package->number_of_registration){
                    $type = MobileAppUser::DISTRIBUTION_CONSUMER;
                }
            }
        }
        $user = MobileAppUser::create($data + ['status'=> MobileAppUser::STATUS_PENDING, 'type'=> $type]);

        $token = mt_rand(100000,999999);
        $user->update([
            'verification_token'    =>  $token
        ]);

        $user->notify(new MobileAppUserRegistrationNotification($token));

        // return response(new MobileAppUserResource($user->fresh()), Response::HTTP_CREATED);
        return response()->json(['status' => true], Response::HTTP_CREATED);
    }

    public function checkStore(Request $request)
    {
        $request->validate([
            'store_id'          =>  ['required',]
        ]);

        $salesPartner = SalesPartner::active()->where('id', $request->store_id)->first();

        if(!$salesPartner){
            return response([
                'message'   =>  'Store Not Found',
                'found'   =>  false,
                'status'    =>  false
            ], 404);
        }

        if($salesPartner->status != SalesPartner::STATUS_ACTIVE){
            return response([
                'message'   =>  'Store Not Active',
                'active'    =>  false,
                'status'    =>  false
            ], 403);
        }
    }



    /**
     * Check username and email exists
     *
     * @param Request $request
     * @return void
     */
    public function checkUser(Request $request)
    {
        $request->validate([
            'column'     =>  ['required', 'in:username,email'],
            'value'     =>  ['required']
        ]);

        // return $request->all();

        $user = MobileAppUser::where($request->column, $request->value)->first();

        if ($user) {
            return response(['status' => true, 'is_active'=>  $user->status == 'active' ], 200);
        }

        return response(['status' => false, 'is_active'=>false], 200);
    }




    /**
     * Verify User OTP
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

        $user  = MobileAppUser::where('email', $request->email)
            ->where('verification_token', $request->otp)
            ->first();

        if(!$user){
            return response(['status' => false], 200);
        }

        return response(['status' => true], 200);
    }




    /**
     * Complete mobile app user registration
     *
     * @param Request $request
     * @return void
     */
    public function registrationComplete(Request $request)
    {
        $request->validate([
            'email'     =>  ['required', 'email',],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', 'same:password']
        ]);

        $user  = MobileAppUser::where('email', $request->email)
            ->where('status', MobileAppUser::STATUS_PENDING)
            ->first();

        if(!$user){
            return response(['status' => false, 'message'   =>  'Invalid user'], 404);
        }


        $user->update([
            'password'  =>  bcrypt($request->password),
            'verification_token'  =>  null,
            'status' => MobileAppUser::STATUS_ACTIVE,
        ]);

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return response([
            'data'  =>  new MobileAppUserResource($user->fresh()),
            'status'    =>  true,
        ], Response::HTTP_ACCEPTED);
    }




    /**
     * Resend Registration OTP
     *
     * @param Request $request
     * @return void
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email'     =>  ['required', 'exists:mobile_app_users,email']
        ]);

        $mobile_app_user = MobileAppUser::where('email', $request->email)
            ->where('status', MobileAppUser::STATUS_PENDING)
            ->first();

        if(!$mobile_app_user){
            return response([
                'status'    =>  false,
            ], Response::HTTP_NOT_FOUND);
        }

        $token = mt_rand(100000,999999);

        $mobile_app_user->update([
            'verification_token'    =>  $token
        ]);

        $mobile_app_user->notify(new MobileAppUserRegistrationNotification($token));

        return response([
            'status'    =>  true,
        ], Response::HTTP_OK);
    }
}
