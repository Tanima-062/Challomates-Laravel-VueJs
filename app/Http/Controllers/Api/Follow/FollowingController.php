<?php

namespace App\Http\Controllers\Api\Follow;

use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Follow\UserFollowingResource;

class FollowingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        // $followings = $user->followings()
        //         ->UserFollowings()
        //         ->paginate(request('per_page', 10));

        // return $followings;

        $followings = Follower::UserFollowings()
            ->accept()
            ->WhereFollower($user->id)
            ->whereHas('followable', function($q){
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->whereNotIn('followable_id', $blocked_users_id)
            ->with('followable')
            ->paginate(request('per_page', 10));

        return UserFollowingResource::collection($followings)
            ->additional([
                'status'    =>  true
            ])
        ;
    }


    public function count()
    {
        $user = request()->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        // $totalFollowings = $user->followings()
        //         ->UserFollowings()
        //         ->count();

        $totalFollowings = Follower::UserFollowings()
            ->accept()
            ->WhereFollower($user->id)
            ->whereHas('followable', function($q){
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->whereNotIn('followable_id', $blocked_users_id)
            ->count()
            ;

        // return $totalFollowers;
        return response([
            'following_count'    =>  $totalFollowings,
            'status'    =>  true
        ]);
    }



    public function remove(Request $request, MobileAppUser $mobile_app_user)
    {
        $user = request()->user();

        DB::table('followers')
            ->where('followable_id', $mobile_app_user->id)
            ->where('followable_type', MobileAppUser::class)
            ->where('follower_id', $user->id)
            ->where('follower_type', MobileAppUser::class)
            ->delete()
        ;

        return response([
            'status'    =>  true
        ], 200);
    }

}
