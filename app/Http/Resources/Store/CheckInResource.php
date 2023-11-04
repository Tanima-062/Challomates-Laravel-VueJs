<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckInResource extends JsonResource
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
            'id'        =>  $this->id,
            'store_id'  =>  $this->sales_partner_id,
            'check_in_time'  =>  $this->check_in_time,
            'store_name'    =>  $this->salesPartner->company_name,
            'photo_url'    =>  $this->salesPartner->profile_picture_url,

        ];
    }
}
