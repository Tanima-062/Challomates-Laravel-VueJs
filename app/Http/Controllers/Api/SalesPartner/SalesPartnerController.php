<?php

namespace App\Http\Controllers\Api\SalesPartner;

use App\Models\Story;
use App\Models\Follower;
use App\Traits\UploadAble;
use App\Models\StoreVisits;
use App\Models\SalesPartner;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Story\StoryResource;
use App\Http\Resources\Store\CheckInResource;
use App\Http\Resources\Store\CheckOutResource;
use MatanYadaev\EloquentSpatial\Objects\Point;

use App\Http\Resources\Store\SalesPartnerResource;
use App\Http\Resources\Store\MobileAppFollowerResource;
use App\Http\Resources\Store\StoreCheckInFollowerResource;
use App\Http\Resources\Store\MobileAppuserFollowingResource;
use App\Http\Resources\Store\StoreMobileAppuserFollowerResource;
use App\Http\Resources\MobileAppUser\MobileAppUserSearchResource;

class SalesPartnerController extends Controller
{
    use UploadAble;

    /**
     * Create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'check_in_time'     =>  'date_format:H:i:s',
            'store_id'          =>  ['required',]
        ]);

        $salesPartner = SalesPartner::where('id', $request->store_id)->first();

        if (!$salesPartner) {
            return response([
                'message'   =>  'Store Not Found',
                'status'    =>  false
            ], 404);
        }

        if ($salesPartner->status != SalesPartner::STATUS_ACTIVE || !$salesPartner->currentContract) {
            return response([
                'active'    =>  false,
                'status'    =>  false
            ], 403);
        }

        $user = $request->user();

        // return $salesPartner->currentContract;

        $contracts_id = $salesPartner->currentContract ?  $salesPartner->currentContract->id : null;

        // $sentTime = now()->addMinutes(5);
        $sentTime = now()->addHours(3);
        $storeVisits = $salesPartner->storeVisits()->create([
            'mobile_app_user_id'    => $user->id,
            'contract_id'           => $contracts_id,
            'check_in_time'         => Carbon::createFromFormat('H:i:s', $request->check_in_time)->toDateTimeString(),
            // 'check_in_time'         => now(),
            // 'sent_time'             =>  now()->addHours(3),
            'sent_time'             =>  $sentTime,
        ]);

        if ($salesPartner->coordinates) {
            $user->update([
                'location'  =>  new Point($salesPartner->latitude, $salesPartner->longitude)
            ]);
        }

        // return response(['success'=>true], 201);
        return (new CheckInResource($storeVisits))->additional([
            'status'    =>  true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'check_out_time'        =>  'date_format:H:i:s',
            'check_in_id'           =>  ['required', 'exists:store_visits,id'],
            'turnover'              =>  ['nullable', 'numeric'],
            'receipt'               =>  ['nullable', 'mimes:png,jpg', 'max:5120'],
            'store_id'              =>  ['required', 'exists:sales_partners,id'],
            'checkout_type'         =>  ['required', 'in:automatic,manual'],
        ]);

        $user = $request->user();
        $salesPartner = SalesPartner::active()->where('id', $request->store_id)->first();
        if (!$salesPartner) {
            return response([
                'message'   =>  'Store Not Found',
                'status'    =>  false
            ], 404);
        }

        $storeVisits = StoreVisits::where('id', $request->check_in_id)
            ->where('mobile_app_user_id', $user->id)
            ->whereNull('check_out_time')
            ->first();

        if (!$storeVisits) {
            return response(['success' => false, 'message' => 'Invalid check-in ID'], 404);
        }

        $mobileAppUser = MobileAppUser::where('id', $storeVisits->mobile_app_user_id)->first();
        $marketing_fee = $storeVisits->salesPartner->currentContract->marketingFee;
        $fee = $mobileAppUser->type == 'direct_consumer' ? $marketing_fee->direct_consumers_share_jackpot : $marketing_fee->distribution_consumers_share_jackpot;

        $jackpot_share = 0;
        $senior_partner_share = 0;
        $challomate_share = 0;
        $consultant_share = 0;
        $sales_partner_share = 0;
        $challomate_marketing_ag_share = 0;

        if ($request->turnover) {
            $jackpot_share = $request->turnover * ($fee / 100);

            if ($mobileAppUser->type == 'direct_consumer') {
                $senior_partner_share = $request->turnover * ($marketing_fee->direct_consumers_senior_partner_share / 100);
                $challomate_share = $request->turnover * ($marketing_fee->direct_consumers_share_fee_challomates / 100);
                $challomate_marketing_ag_share = $request->turnover * ($marketing_fee->direct_consumers_share_challomates_marketing_ag / 100);
            } else {
                $consultant_share = $request->turnover * ($marketing_fee->distribution_consumers_share_of_consultants / 100);
                $sales_partner_share = $request->turnover * ($marketing_fee->distribution_consumers_proportion_of_sales_partners / 100);
                $challomate_marketing_ag_share = $request->turnover * ($marketing_fee->distribution_consumers_share_challomates_marketing_ag / 100);
            }
        }


        $data = [
            'check_out_time' => Carbon::createFromFormat('H:i:s', $request->check_out_time)->toDateTimeString(),
            // 'check_out_time'=> now(),
            'turnover' => $request->turnover ?? 0,
            'jackpot_share' => $jackpot_share,
            'senior_partner_share' => $senior_partner_share,
            'challomate_share' => $challomate_share,
            'consultant_share' => $consultant_share,
            'sales_partner_share' => $sales_partner_share,
            'challomate_marketing_ag_share' => $challomate_marketing_ag_share,
            'checkout_type' =>  $request->checkout_type,
        ];

        if ($request->hasFile('receipt')) {
            $data['receipt'] =  $this->uploadOne($request->receipt, 'receipt', env('FILESYSTEM_DISK', 'public'));
        }

        $storeVisits->load('stories.media');


        $checkout_reward = 0;
        $posting_coin = 0;



        // if($request->checkout_type == 'manual' && $request->hasFile('receipt')){
        $reward = 0;
        foreach ($storeVisits->stories as $story) {
            foreach ($story->media as $media) {
                //image = 3
                //video = 7
                if ($media->media_type == 'image') {
                    $reward = 3;
                } else if ($media->media_type == 'video') {
                    $reward = 7;
                    break;
                }
            }
        }

        $today_reward = StoreVisits::where('mobile_app_user_id', $user->id)
            ->whereDay('check_in_time', today())
            ->sum('coin');
        $limit = 50;
        $limit_over = 50;
        $limit_cross = false;


        if ($today_reward < $limit) {
            $can_max_reward = $limit - $today_reward;

            if ($can_max_reward < $reward) {
                $checkout_reward = $can_max_reward;
                $limit_cross = true;
            } else {
                $checkout_reward = $reward;
            }
        } else {
            $limit_cross = true;
        }

        if ($salesPartner->currentContract && $request->hasFile('receipt')) {
            $package = $salesPartner->currentContract->package;

            $posting_coin = ($request->turnover * $package->coin_factor);
        }
        // }

        // $is_limit_over = $today_reward >= $limit_over ? true : false;
        $is_limit_over = $limit_cross;
        $storeVisits->update($data + ['coin' => $checkout_reward, 'posting_coins' => $posting_coin, 'is_limit_over' => $is_limit_over,  'jackpot_share' => ($user->type == 'distribution_consumer') ? ($data['turnover'] * (float)$storeVisits?->contract?->marketingFee?->distribution_consumers_share_jackpot) : ($data['turnover'] * (float)$storeVisits?->contract?->marketingFee?->direct_consumers_share_jackpot)]);

        $user->update([
            'coin'  =>  $user->coin + $checkout_reward + $posting_coin,
            'location'  =>  null,
            // 'posting_coins' =>
        ]);

        return (new CheckOutResource($storeVisits->fresh()))->additional([
            'status'    =>  true
        ]);
    }



    public function show($salesPartner)
    {
        $salesPartner = SalesPartner::with(['openingHours', 'branch:id,name', 'branchCategory:id,name'])
            ->active()
            ->where('id', $salesPartner)
            ->firstOrFail();


        /**
         * Follower mates count
         */
        $follower_mates_count = Follower::StoreFollowing()
            ->WhereFollowabble($salesPartner->id)
            ->whereHas('followerable', function ($q) {
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            // ->with('followerable')
            ->count();
        // return $follower_mates_count;

        $checkin_store_visits = DB::table('store_visits')
            ->select(['mobile_app_user_id'])
            ->whereNull('check_out_time')
            ->pluck('mobile_app_user_id')
            ->unique()
            ->values()
            ->toArray();
        // return $checkin_store_visits;


        /**
         * Old Code


            $mates_live_on_site_count = Follower::StoreFollowing()
                    ->WhereFollowabble($salesPartner->id)
                    ->WhereFollowerIn($checkin_store_visits)
                    ->whereHas('followerable', function($q){
                        $q->where('status', MobileAppUser::STATUS_ACTIVE);
                    })
                    ->count();
                    // return $mates_live_on_site_count;

            $checkout_store_visits = DB::table('store_visits')
                ->select(['mobile_app_user_id'])
                ->whereNotNull('check_out_time')
                ->pluck('mobile_app_user_id')
                ->unique()
                ->values()
                ->toArray();
            // return $checkout_store_visits;

            $number_of_all_mates_count = Follower::StoreFollowing()
                        ->WhereFollowabble($salesPartner->id)
                        ->WhereFollowerIn($checkout_store_visits)
                        ->whereHas('followerable', function($q){
                            $q->where('status', MobileAppUser::STATUS_ACTIVE);
                        })
                        ->count();
            // return $number_of_all_mates_count;

         */
        $auth__user = request()->user();


        /**
         * Mates live on site count
         */
        $mates_live_on_site_count = 0;
        $checkin_store_visits = DB::table('store_visits')
            ->select(['mobile_app_user_id'])
            ->whereNull('check_out_time')
            ->where('sales_partner_id', $salesPartner->id)
            ->pluck('mobile_app_user_id')
            ->unique()
            ->values()
            ->toArray();


        $followings = Follower::query()
            ->UserFollowings()
            ->UserFollower()
            ->WhereFollowabbleIn($checkin_store_visits)
            ->WhereFollower($auth__user->id)
            ->Accept()
            ->select('followable_id')
            ->pluck('followable_id')
            ->toArray();

        $mates_live_on_site_count = MobileAppUser::query()
            ->active()
            ->FollowByMe($auth__user->id)
            ->FollowByMate($auth__user->id)
            ->whereIn('id', $followings)
            ->count();



        /**
         * All mates count
         */
        $checkout_store_visits = DB::table('store_visits')
            ->where('sales_partner_id', $salesPartner->id)
            ->select(['mobile_app_user_id'])
            ->whereNotNull('check_out_time')
            ->pluck('mobile_app_user_id')
            ->unique()
            ->values()
            ->toArray();

        $number_of_all_mates_count = Follower::UserFollowings()
            ->Accept()
            ->UserFollower()
            ->WhereFollowabbleIn($checkout_store_visits)
            ->WhereFollower($auth__user->id)
            ->whereHas('followable', function ($q) use ($auth__user) {
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->count();






        $follow = DB::table('followers')
            ->where('followable_id', $salesPartner->id)
            ->where('followable_type', SalesPartner::class)
            ->where('follower_id', $auth__user->id)
            // ->where('follower_type', MobileAppUser::class)
            ->first();

        $status = null;
        if ($follow) {
            $status = $follow->status;
        }

        return (new SalesPartnerResource($salesPartner))->additional([
            'status'    =>  true,
            'data'  =>  [
                'follow_status' => $status,
                'follower_mates_count'  =>  $follower_mates_count,
                'mates_live_on_site_count'  =>  $mates_live_on_site_count,
                'number_of_all_mates_count'  =>  $number_of_all_mates_count,
            ]
        ]);
    }


    /**
     * Get nearest store by user location
     *
     * @param Request $request
     * @return void
     */
    public function getNearestStore(Request $request)
    {
        $request->validate([
            'lat'       =>  ['required', 'numeric'],
            'lon'       =>  ['required', 'numeric'],
            'radius'       =>  ['nullable', 'numeric'],
        ]);

        $userLocation = new Point($request->lat, $request->lon);
        $radious = request('radius', 1); //km
        $salesPartner = SalesPartner::query()
            ->whereDistanceSphere('coordinates',  $userLocation, '<=', ($radious * 1000))
            ->active()
            ->select(['id', 'company_name',  'profile_picture', 'coordinates', 'website', 'street', 'house_number', 'zip_code', 'city', 'country'])
            ->get();


        return (SalesPartnerResource::collection($salesPartner))->additional([
            'status'    =>  true
        ]);
    }


    public function getCheckInNow(Request $request, $salesPartner)
    {
        $salesPartner = SalesPartner::active()->where('id', $salesPartner)->firstOrFail();

        // $storeVisits = $salesPartner
        //     ->storeVisits()
        //     ->with('mobileAppUser', function($q){
        //         $q->select(['id','username','first_name', 'last_name']);
        //         $q->where('status', MobileAppUser::STATUS_ACTIVE);
        //     })
        //     ->whereHas('mobileAppUser', function($q){
        //         $q->where('status', MobileAppUser::STATUS_ACTIVE);
        //     })
        //     ->whereNull('check_out_time')
        //     ->paginate(request('per_page', 10));

        // return $storeVisits;

        // return $salesPartner;

        $checkInIds = DB::table('store_visits')
            // ->whereNull('check_out_time')
            ->where('sales_partner_id', $salesPartner->id)
            ->pluck('mobile_app_user_id')
            ->unique()
            ->flatten();
        // return $checkInIds;

        $user = $request->user();

        $followers = $user->AcceptedFollowers()
            ->UserFollowers()
            ->whereIn('follower_id', $checkInIds)
            // ->paginate(request('per_page', 10))
            ->get();

        return StoreCheckInFollowerResource::collection($followers);
    }




    public function getReceiptData(Request $request)
    {
        $request->validate([
            'text'          =>  ['required',],
            'store_id'      =>  ['required']
        ]);


        return response([
            'store_name'    =>  uniqid('STORE_'),
            'status'        =>  true
        ]);


        // $salesPartner = SalesPartner::active()->where('id', $request->store_id)->firstOrFail();
        if (!$salesPartner = SalesPartner::active()->where('id', $request->store_id)->first()) {
            return response([
                'store_found'       =>  false,
                'status'    =>  false
            ], 404);
        }

        // if(!$ocrTemplate = $salesPartner->receiptOcr){
        //     return response([
        //         'message'       =>  'OCR template not found',
        //         'status'        =>  false
        //     ], 404);
        // }

        // $value = '';
        // $end_text = $ocrTemplate->end_text ?? '';
        // $pattern = "~\b{$ocrTemplate->start_text}([\s\S]*?.+)({$end_text})?\b~i";

        // if(preg_match($pattern, $request->text, $amount)){
        //     dd($amount);
        //     if(preg_match("@(\d+.?\d+)@", $amount[1], $match)){
        //         $value = $match[1];
        //     }
        // }

        $value = '';
        $end_text = '';
        $start_text = 'total';
        $pattern = "~\b{$start_text}([\s\S]*?.+)({$end_text})?\b~i";

        if (preg_match($pattern, $request->text, $amount)) {
            if (preg_match("@(\d+.?\d+)@", $amount[1], $match)) {
                $value = $match[1];
            }
        }

        if ($value == '' || $value == null) {
            return response([
                'match_found'       =>  false,
                'status'    =>  false
            ], 404);
        }

        return response([
            'store_name'    =>  $salesPartner->company_name,
            'total'         =>  $value,
            'status'        =>  true
        ]);
    }



    public function getFollowerMates($salesPartner)
    {
        $salesPartner = SalesPartner::with(['openingHours', 'branch:id,name', 'branchCategory:id,name'])
            ->active()
            ->where('id', $salesPartner)
            ->firstOrFail();

        $follower_mates = Follower::StoreFollowing()
            ->WhereFollowabble($salesPartner->id)
            ->whereHas('followerable', function ($q) {
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->with('followerable')
            ->paginate(request('per_page', 10));

        // return $follower_mates;

        return MobileAppFollowerResource::collection($follower_mates)->additional([
            'status'        =>  true
        ]);
    }



    public function getMatesLiveOnSite($salesPartner)
    {
        $user = request()->user();
        $salesPartner = SalesPartner::with(['openingHours', 'branch:id,name', 'branchCategory:id,name'])
            ->active()
            ->where('id', $salesPartner)
            ->firstOrFail();

        $checkin_store_visits = DB::table('store_visits')
            ->select(['mobile_app_user_id'])
            ->whereNull('check_out_time')
            ->where('sales_partner_id', $salesPartner->id)
            ->pluck('mobile_app_user_id')
            ->unique()
            ->values()
            ->toArray();

        // $mates_live_on_site = Follower::StoreFollowing()
        //     ->WhereFollowabble($salesPartner->id)
        //     ->WhereFollowerIn($checkin_store_visits)
        //     ->whereHas('followerable', function($q){
        //         $q->where('status', MobileAppUser::STATUS_ACTIVE);
        //     })
        //     ->with('followerable')
        //     ->paginate(request('per_page', 10));

        // return $checkin_store_visits;

        $followings = Follower::query()
            ->UserFollowings()
            ->UserFollower()
            ->WhereFollowabbleIn($checkin_store_visits)
            ->WhereFollower($user->id)
            ->Accept()
            ->select('followable_id')
            ->pluck('followable_id')
            ->toArray();
        // return $followings;


        $mates_live_on_site = MobileAppUser::query()
            ->active()
            ->FollowByMe($user->id)
            ->FollowByMate($user->id)
            ->whereIn('id', $followings)
            ->paginate(request('per_page', 10));;


        return StoreMobileAppuserFollowerResource::collection($mates_live_on_site)->additional([
            'status'        =>  true
        ]);
    }

    public function getAllMates($salesPartner)
    {
        $salesPartner = SalesPartner::with(['openingHours', 'branch:id,name', 'branchCategory:id,name'])
            ->active()
            ->where('id', $salesPartner)
            ->firstOrFail();

        $checkout_store_visits = DB::table('store_visits')
            ->where('sales_partner_id', $salesPartner->id)
            ->select(['mobile_app_user_id'])
            ->whereNotNull('check_out_time')
            ->pluck('mobile_app_user_id')
            ->unique()
            ->values()
            ->toArray();

        // return $checkout_store_visits;
        // $all_mates = Follower::StoreFollowing()
        //     ->WhereFollowabble($salesPartner->id)
        //     ->WhereFollowerIn($checkout_store_visits)
        //     ->whereHas('followerable', function($q){
        //         $q->where('status', MobileAppUser::STATUS_ACTIVE);
        //     })
        //     ->with('followerable')
        //     ->paginate(request('per_page', 10));

        // return $checkout_store_visits;

        // return MobileAppFollowerResource::collection($all_mates)->additional([
        //     'status'        =>  true
        // ]);

        $user = request()->user();
        $all_mates = Follower::UserFollowings()
            ->Accept()
            ->UserFollower()
            ->WhereFollowabbleIn($checkout_store_visits)
            ->WhereFollower($user->id)
            ->whereHas('followable', function ($q) use ($user) {
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->with(['followable' => fn ($q) => $q->FollowByMe($user->id)])
            ->paginate(request('per_page', 10));

        return MobileAppuserFollowingResource::collection($all_mates)->additional([
            'status'        =>  true
        ]);
    }


    public function follow($salesPartner)
    {
        $salesPartner = SalesPartner::with(['openingHours', 'branch:id,name', 'branchCategory:id,name'])
            ->active()
            ->where('id', $salesPartner)
            ->firstOrFail();

        $auth__user = request()->user();

        $follow = DB::table('followers')
            ->where('followable_id',  $salesPartner->id)
            ->where('followable_type', SalesPartner::class)
            ->where('follower_id',  $auth__user->id)
            ->where('follower_type', MobileAppUser::class)
            ->first();

        if ($follow) {
            return response([
                'message'   =>  'Already followed',
                'status'    =>  false
            ], 422);
        }

        // return $salesPartner;
        $follow = Follower::create([
            'followable_id'     =>  $salesPartner->id,
            'followable_type'   =>  SalesPartner::class,
            'follower_id'       =>  $auth__user->id,
            'follower_type'     =>  MobileAppUser::class,
            'status'            =>  Follower::ACCEPT,
            'accept_at'         =>  now(),
        ]);

        return response([
            'status'    =>  true
        ], 201);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request, $salesPartner)
    {
        $user = request()->user();

        $salesPartner = SalesPartner::with(['openingHours', 'branch:id,name', 'branchCategory:id,name'])
            ->active()
            ->where('id', $salesPartner)
            ->firstOrFail();

        DB::table('followers')
            ->where('followable_id',  $salesPartner->id)
            ->where('followable_type', SalesPartner::class)
            ->where('follower_id',  $user->id)
            ->where('follower_type', MobileAppUser::class)
            ->delete()
            // ->get()
        ;

        return response([
            'status'    =>  true
        ], 200);
    }


    public function checkQr(Request $request, $salesPartner)
    {
        $salesPartner = SalesPartner::where('id', $salesPartner)->first();

        // return $salesPartner;

        if (!$salesPartner) {
            return response([
                'found'     =>  false,
                'status'    =>  false,
                'active'    =>  false,
            ], 200);
        }

        if ($salesPartner->status != SalesPartner::STATUS_ACTIVE) {
            return response([
                'found'    =>  true,
                'active'    =>  false,
                'status'    =>  false
            ], 200);
        }

        return response([
            'found'     =>  true,
            'active'    =>  true,
            'status'    =>  true
        ]);
    }




    /**
     * Get stories by store check-in users stories
     *
     * @param Request $request
     * @param int $salesPartner
     * @return void
     */
    public function getStories(Request $request, $salesPartner)
    {
        $salesPartner = SalesPartner::where('id', $salesPartner)->firstOrFail();
        $user = $request->user();

        $stories = Story::query()
            ->orderBy('created_at', 'DESC')
            ->with('creator', 'media')
            ->withCount([
                'comments' => fn ($q) => $q->whereHas('user', fn ($q) => $q->where('status', MobileAppUser::STATUS_ACTIVE)),
                'likes' => fn ($q) => $q->whereHas('user', fn ($q) => $q->where('status', MobileAppUser::STATUS_ACTIVE))
            ])
            ->isLike($user)
            ->isTagged($user)
            ->where('sales_partner_id', $salesPartner->id)
            ->paginate(request('per_page', 10));

        return StoryResource::collection($stories)->additional([
            'status' =>  true
        ]);
    }
}
