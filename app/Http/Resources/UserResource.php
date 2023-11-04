<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'prefixed_id' => $this->prefixed_id,
            'status' => $this->status,
            'type' => $this->type,
            'name' => $this->name,
            'full_phone_number' => $this->full_phone_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            "country_iso_code" => $this->country_iso_code,
            "created_at" => $this->created_at,
            "email" => $this->email,
            "email_verified_at" => $this->phone_number,
            "fcm_token" => $this->fcm_token,
            "avatar_url" => $this->avatar_url
        ];
    }
}
