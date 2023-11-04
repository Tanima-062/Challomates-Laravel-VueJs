<?php

namespace App\Http\Resources\Story;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryLikeUserResource extends JsonResource
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
            'id'                    => $this->id,
            'photo_url'             => $this->photo_url,
            'username'              => $this->username,
            'name'                  => $this->name,
            'privacy'               => $this->privacy,
            'follow_by_me_status'   => $this->follow_by_me
        ];
    }
}
