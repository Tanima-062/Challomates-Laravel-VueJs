<?php

namespace App\Http\Controllers\Api\Follow;

use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Follow\FollowRequestResource;
use App\Http\Resources\Follow\UserFollowerResource;

class FollowerController extends Controller
{
    /**
     * Display a listing of the resource.
     * Get Authenticated user followrs
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        // $followers = $user->followers()
        //         ->UserFollowers()
        //         ->paginate(request('per_page', 10))
        //     ;
        // return $followers;

        if($request->user_id){
            $user = MobileAppUser::active()->where('id', $request->user_id)->firstOrFail();
        }

        $followers = Follower::UserFollowings()
            ->accept()
            ->WhereFollowabble($user->id)
            ->whereNotIn('follower_id', $blocked_users_id)
            ->whereHas('followerable', function($q){
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->with('followerable')
            ->paginate(request('per_page', 10));

        // return $followers;

        return UserFollowerResource::collection($followers)
            ->additional([
                'status'    =>  true
            ])
        ;
    }
    /**
     * Display a listing of the resource.
     * Get Authenticated user accepted followrs
     *
     * @return \Illuminate\Http\Response
     */
    public function getAcceptedFollowers(Request $request)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        // $followers = $user->AcceptedFollowers()
        //     ->UserFollowers()
        //     ->paginate(request('per_page', 10))
        // ;

        $followers = Follower::UserFollowings()
            ->accept()
            ->WhereFollowabble($user->id)
            ->whereNotIn('follower_id', $blocked_users_id)
            ->whereHas('followerable', function($q){
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->with('followerable')
            ->paginate(request('per_page', 10));


        return UserFollowerResource::collection($followers)
            ->additional([
                'status'    =>  true
            ])
        ;
    }


    public function getPendingFollowers(Request $request)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

         $followers = Follower::UserFollowings()
         ->pending()
         ->WhereFollowabble($user->id)
         ->whereNotIn('follower_id', $blocked_users_id)
         ->whereHas('followerable', function($q){
             $q->where('status', MobileAppUser::STATUS_ACTIVE);
         })
         ->with('followerable')
         ->paginate(request('per_page', 10));

        return FollowRequestResource::collection($followers)->additional([
            'status'    =>  true
        ]);

    }



    public function count()
    {
        $user = request()->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        // $totalFollowers = $user->followers()
        //         ->UserFollowers()
        //         ->count();

        $totalFollowers = Follower::UserFollowings()
        ->pending()
        ->WhereFollowabble($user->id)
        ->whereNotIn('follower_id', $blocked_users_id)
        ->whereHas('followerable', function($q){
            $q->where('status', MobileAppUser::STATUS_ACTIVE);
        })
        ->count()
        ;
        // return $totalFollowers;
        return response([
            'follower_count'    =>  $totalFollowers,
            'status'    =>  true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function follow(Request $request, MobileAppUser $mobile_app_user)
    {
        $user = request()->user();

        $follow = DB::table('followers')
            ->where('followable_id', $mobile_app_user->id)
            ->where('followable_type', MobileAppUser::class)
            ->where('follower_id', $user->id)
            ->where('follower_type', MobileAppUser::class)
            ->first();

        if($follow){
            return response([
                'message'   =>  'Already followed',
                'status'    =>  false
            ], 422);
        }

        // $mobile_app_user->followers()
        //     ->attach($user->id, [
        //         'followable_type'   =>  MobileAppUser::class,
        //         'follower_type'   =>  MobileAppUser::class,
        //         'status'            =>  Follower::PENDING,
        //     ])
        // ;

        // return $salesPartner;
        Follower::create([
            'followable_id'     =>  $mobile_app_user->id,
            'followable_type'   =>  MobileAppUser::class,
            'follower_id'       =>  $user->id,
            'follower_type'     =>  MobileAppUser::class,
            'status'            =>  Follower::PENDING,
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
    public function accept(Request $request, MobileAppUser $mobile_app_user)
    {
        $user = request()->user();


        $follow = DB::table('followers')
            ->where('followable_id',  $user->id)
            ->where('followable_type', MobileAppUser::class)
            ->where('follower_id',  $mobile_app_user->id)
            ->where('follower_type', MobileAppUser::class)
            ->first();


        if(!$follow){
            return response([
                'message'   =>  'User not found',
                'status'    =>  false
            ], 404);
        }

        DB::table('followers')
            ->where('followable_id',  $user->id)
            ->where('follower_id',  $mobile_app_user->id)
            ->update([
            'status'    =>  Follower::ACCEPT,
            'accept_at'    =>  now(),
        ]);

        return response([
            'status'    =>  true
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, MobileAppUser $mobile_app_user)
    {
        $user = request()->user();


        $follow = DB::table('followers')
            ->where('followable_id',  $user->id)
            ->where('followable_type', MobileAppUser::class)
            ->where('follower_id',  $mobile_app_user->id)
            ->where('follower_type', MobileAppUser::class)
            ->first();

        if(!$follow){
            return response([
                'message'   =>  'User not found',
                'status'    =>  false
            ], 404);
        }

        DB::table('followers')
            ->where('followable_id',  $user->id)
            ->where('follower_id',  $mobile_app_user->id)
            // ->update([
            //     'status'    =>  Follower::REJECT,
            // ])
            ->delete()
        ;

        return response([
            'status'    =>  true
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Follower  $follower
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request, MobileAppUser $mobile_app_user)
    {
        $user = request()->user();

        DB::table('followers')
            ->where('followable_id',  $user->id)
            ->where('followable_type', MobileAppUser::class)
            ->where('follower_id',  $mobile_app_user->id)
            ->where('follower_type', MobileAppUser::class)
            ->delete()
        ;

        return response([
            'status'    =>  true
        ], 200);
    }
}
