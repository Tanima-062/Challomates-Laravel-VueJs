<?php

namespace App\Http\Resources\MobileAppUser;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileAppUserSearchResource extends JsonResource
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
            'prefix_id'         => $this->prefix_id,
            'photo_url'         => $this->photo_url,
            'username'          => $this->username,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'name'              => $this->name,
            'date_of_birth'     =>  $this->date_of_birth->format('d.m.Y'),
            'email'             => $this->email,
            'street'            => $this->street,
            'house_number'      => $this->house_number,
            'zip_code'          => $this->zip_code,
            'city'              => $this->city,
            'country'           => $this->country,
            'country_iso_code'  => $this->country_iso_code,
            'phone_number'      => $this->phone_number,
            'full_phone_number' => $this->full_phone_number,
            'type'              => $this->type,
            'privacy'           => $this->privacy,
            'language_id'       => $this->language_id,
            'status'            => $this->status,
            'created_at'        => $this->created_at,
            'follow_by_me'      =>  $this->follow_by_me,
            'follow_by_mate'      =>  $this->follow_by_mate,
        ];
    }
}
