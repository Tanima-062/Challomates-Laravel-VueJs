<?php

namespace App\Http\Resources\Story;

use App\Http\Resources\Store\SalesPartnerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BoosterPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $request->user();

        return [
            'id'            =>  $this->id,
            'store_id'            =>  $this->sales_partner_id,
            // 'check_in_id'            =>  $this->check_in_id,
            'title'         =>  $this->title,
            'body'          =>  $this->body,
            'created_at'    =>  $this->created_at,
            'image_url'     =>  $this->file_url,
            "comments_count"=>  $this->comments_count,
            "likes_count"   =>  $this->likes_count,
            "like_by_me"    =>  $this->like_by_me ? true: false,
            // 'store'         =>  [
            //     'id'        => $salesPartner->id,
            //     'name'      =>  $salesPartner->company_name,
            // ],
            'store'         =>  $this->whenLoaded('salesPartner', function(){
                return new SalesPartnerResource($this->salesPartner);
            })

        ];
    }
}
