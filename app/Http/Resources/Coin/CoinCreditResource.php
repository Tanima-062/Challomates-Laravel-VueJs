<?php

namespace App\Http\Resources\Coin;

use App\Http\Resources\Store\SalesPartnerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CoinCreditResource extends JsonResource
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
            // 'sales_coin'        =>  'under construction',
            // 'sales_coin'        =>  $this->posting_coins,
            'sales_coin'        =>  (string) number_format($this->posting_coins, 2, '.', ''),
            // 'post_coin'         =>  $this->coin,
            'post_coin'         =>  (string) number_format($this->coin, 2, '.', ''),
            // 'total_coin'        =>  ($this->coin + $this->posting_coins),
            // 'is_limit_over'     =>  (bool)  ($this->checkout_type == 'manual' && $this->coin == 0),
            'is_limit_over'     =>  $this->is_limit_over,
            'total_coin'        =>  (string) number_format( ($this->coin + $this->posting_coins), 2, '.', ''),
            'created_at'        =>  $this->created_at,
            'store'             =>  $this->whenLoaded('salesPartner', function(){
                return new  SalesPartnerResource($this->salesPartner);
            })
        ];
    }
}
