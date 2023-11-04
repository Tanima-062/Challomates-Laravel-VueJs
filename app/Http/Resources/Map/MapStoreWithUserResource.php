<?php

namespace App\Http\Resources\Map;

use Illuminate\Http\Resources\Json\JsonResource;

class MapStoreWithUserResource extends JsonResource
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
            'id'                =>  $this->id,
            'name'              =>  $this->company_name,
            'photo_url'         =>  $this->profile_picture_url,
            'latitude'          =>  $this->latitude,
            'longitude'         =>  $this->longitude,
            'total_users'       =>  $this->store_visits_count,
            $this->mergeWhen(!is_null($this->website), fn()=> ['website'=>$this->website]),
            $this->mergeWhen(!is_null($this->street), fn()=> ['street'=>$this->street]),
            $this->mergeWhen(!is_null($this->house_number), fn()=> ['house_number'=>$this->house_number]),
            $this->mergeWhen(!is_null($this->zip_code), fn()=> ['zip_code'=>$this->zip_code]),
            $this->mergeWhen(!is_null($this->city), fn()=> ['city'=>$this->city]),
            $this->mergeWhen(!is_null($this->coordinates), fn()=> ['latitude'=>$this->latitude, 'longitude'=>$this->longitude]),
            $this->mergeWhen(!is_null($this->country), fn()=> ['country'=>getCountryNameFromCode($this->country)]),
            'users'             =>  MapMobileAppUserResource::collection($this->storeVisits->pluck('mobileAppUser')),
        ];
    }
}
