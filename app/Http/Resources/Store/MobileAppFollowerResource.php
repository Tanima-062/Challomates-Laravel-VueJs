<?php

namespace App\Http\Resources\Store;

use App\Models\Follower;
use App\Models\MobileAppUser;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileAppFollowerResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $mobile_app_user = $this->follower;

        $user = request()->user();
        $follow_status = null;

        // $follow = Follower::UserFollowings()
        //     ->WhereFollowabble($mobile_app_user->id)
        //     ->WhereFollower($user->id)
        //     ->where('follower_type', MobileAppUser::class)
        //     ->first()
        // ;

        $follow = Follower::query()
        ->where('followable_id', $mobile_app_user->id)
        ->where('followable_type', MobileAppUser::class)
        ->where('follower_id', $user->id)
        ->where('follower_type', MobileAppUser::class)
        ->first()
        ;
        // return $follow;

        if($follow){
            $follow_status = $follow->status;
        }

        return [
            'id'            =>  $this->id,
            'mobile_app_user_id'    =>  $mobile_app_user->id,
            'first_name'    =>  $mobile_app_user->first_name,
            'last_name'    =>  $mobile_app_user->last_name,
            'username'    =>  $mobile_app_user->username,
            'photo_url'     =>  $mobile_app_user->photo_url,
            'privacy'     =>  $mobile_app_user->privacy,
            // 'follow_status' =>  $follow_status,
            'follow_by_me_status' =>  $follow_status,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => true,
        ];
    }
}
