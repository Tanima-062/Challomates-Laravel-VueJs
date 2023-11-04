<?php

namespace App\Http\Resources\Map;

use Illuminate\Http\Resources\Json\JsonResource;

class MapMobileAppUserResource extends JsonResource
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
            'id'                => $this->id,
            'photo_url'         => $this->photo_url,
            'username'          => $this->username,
            'name'              => $this->name,
            'privacy'           =>  $this->privacy,
            // 'latitude'          =>  $this->latitude,
            // 'longitude'         =>  $this->longitude,
        ];
    }
}
