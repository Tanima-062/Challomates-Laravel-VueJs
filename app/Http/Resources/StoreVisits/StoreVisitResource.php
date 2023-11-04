<?php

namespace App\Http\Resources\StoreVisits;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Store\SalesPartnerResource;

class StoreVisitResource extends JsonResource
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
            'store_id'          =>  $this->salesPartner->id,
            'check_in_time'     =>  $this->check_in_time,
            'check_out_time'    =>  $this->check_out_time,
            'turnover'          =>  $this->turnover,
            'created_at'        =>  $this->created_at,
            'store'             =>  $this->whenLoaded('salesPartner', function(){
                return new  SalesPartnerResource($this->salesPartner);
            })
        ];
    }
}
