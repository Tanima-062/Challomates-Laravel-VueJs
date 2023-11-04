<?php

namespace App\Http\Resources\Store;

use App\Models\Follower;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileAppuserFollowingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $mobile_app_user = $this->followable;

        // $user = request()->user();
        $follow_status = null;

        // $follow = Follower::query()
        // ->where('followable_id', $mobile_app_user->id)
        // ->where('followable_type', MobileAppUser::class)
        // ->where('follower_id', $user->id)
        // ->where('follower_type', MobileAppUser::class)
        // ->first()
        // ;

        // if($follow){
        //     $follow_status = $follow->status;
        // }

        return [
            'id'            =>  $this->id,
            'mobile_app_user_id'    =>  $mobile_app_user->id,
            'first_name'    =>  $mobile_app_user->first_name,
            'last_name'    =>  $mobile_app_user->last_name,
            'username'    =>  $mobile_app_user->username,
            'photo_url'     =>  $mobile_app_user->photo_url,
            'privacy'     =>  $mobile_app_user->privacy,
            // 'follow_status' =>  $follow_status,
            'follow_by_me_status' =>  $mobile_app_user->follow_by_me,
        ];
    }
}
