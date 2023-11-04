<?php

namespace App\Http\Resources\Map;

use Illuminate\Http\Resources\Json\JsonResource;

class MapMobileAppUserSearchResource extends JsonResource
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
            'name'              => $this->name,
            'username'          => $this->username,
            'privacy'           =>  $this->privacy,
            // 'check_in_status'   =>  $this->check_in_now  ? true: false,
            'check_in_status'   =>  $this->check_in_now  ? false: true,
            $this->mergeWhen(!is_null($this->location), fn()=> ['latitude'=>$this->latitude, 'longitude'=>$this->longitude]),
            // $this->mergeWhen(!is_null($this->country), fn()=> ['country'=>getCountryNameFromCode($this->country)]),
        ];
    }
}
