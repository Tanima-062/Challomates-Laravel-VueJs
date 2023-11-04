<?php

namespace App\Http\Resources\Follow;

use App\Models\Follower;
use App\Models\MobileAppUser;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowRequestResource extends JsonResource
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

        $follow = Follower::UserFollowings()
            ->WhereFollowabble($mobile_app_user->id)
            ->WhereFollower($user->id)
            ->where('follower_type', MobileAppUser::class)
            ->first()
        ;

        if($follow){
            $follow_status = $follow->status;
        }

        return [
            'id'    =>  $mobile_app_user->id,
            'photo_url'     =>  $mobile_app_user->photo_url,
            'username'    =>  $mobile_app_user->username,
            'first_name'    =>  $mobile_app_user->first_name,
            'last_name'    =>  $mobile_app_user->last_name,
            'name'    =>  $mobile_app_user->name,
            'accept'            =>  $this->status == Follower::ACCEPT,
            'status'    =>  $this->status,
            'follow_by_me_status'   => $follow_status
        ];
    }
}
