<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreMobileAppuserFollowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            // 'id'            =>  $this->id,
            'mobile_app_user_id'    =>  $this->id,
            'first_name'    =>  $this->first_name,
            'last_name'    =>  $this->last_name,
            'username'    =>  $this->username,
            'photo_url'     =>  $this->photo_url,
            'privacy'     =>  $this->privacy,
            'follow_by_me_status' =>  $this->follow_by_me,
            // 'follow_by_mate'    =>  $this->follow_by_mate
            // 'follow_by_me_status' =>  $follow_status,
        ];
    }
}
