<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckOutResource extends JsonResource
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
            'store_name'    =>  $this->salesPartner->company_name,
            'check_in_time'  =>  $this->check_in_time,
            'check_out_time'  =>  $this->check_out_time,
            'turnover'  =>  (string) $this->turnover,
            'receipt_url'   =>  $this->receipt_url,
            'checkout_type' =>  $this->checkout_type
        ];
    }
}
