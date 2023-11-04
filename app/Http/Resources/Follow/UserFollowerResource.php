<?php

namespace App\Http\Resources\Follow;

use App\Models\Follower;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFollowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return  [
        //     'id'                => $this->id,
        //     'photo_url'         => $this->photo_url,
        //     'username'          => $this->username,
        //     'first_name'        => $this->first_name,
        //     'last_name'         => $this->last_name,
        //     'name'              => $this->name,
        //     'accept'            =>  $this->pivot->status == Follower::ACCEPT,
        // ];

        $mobile_app_user = $this->follower;

        return [
            'id'    =>  $mobile_app_user->id,
            'photo_url'     =>  $mobile_app_user->photo_url,
            'username'    =>  $mobile_app_user->username,
            'first_name'    =>  $mobile_app_user->first_name,
            'last_name'    =>  $mobile_app_user->last_name,
            'name'    =>  $mobile_app_user->name,
            'accept'            =>  $this->status == Follower::ACCEPT,
        ];
    }
}
