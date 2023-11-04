<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesPartnerResource extends JsonResource
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
            $this->mergeWhen(!is_null($this->website), fn()=> ['website'=>$this->website]),
            $this->mergeWhen(!is_null($this->street), fn()=> ['street'=>$this->street]),
            $this->mergeWhen(!is_null($this->house_number), fn()=> ['house_number'=>$this->house_number]),
            $this->mergeWhen(!is_null($this->zip_code), fn()=> ['zip_code'=>$this->zip_code]),
            $this->mergeWhen(!is_null($this->city), fn()=> ['city'=>$this->city]),
            $this->mergeWhen(!is_null($this->country), fn()=> ['country'=>getCountryNameFromCode($this->country)]),
            $this->mergeWhen(!is_null($this->coordinates), fn()=> ['latitude'=>$this->latitude, 'longitude'=>$this->longitude]),
            'opening_hours'     => $this->whenLoaded('openingHours', function(){
                return OpeningHoursResource::collection($this->openingHours);
            }),
            'branch'            => $this->whenLoaded('branch', function(){
                return new StoreBranchResource($this->branch);
            }),
            'branch_category'          => $this->whenLoaded('branchCategory', function(){
                return new StoreBranchCategoryResource($this->branchCategory);
            }),
        ];
    }
}
