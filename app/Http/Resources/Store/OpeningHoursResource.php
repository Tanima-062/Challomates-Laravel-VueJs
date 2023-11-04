<?php

namespace App\Http\Resources\Store;

use Illuminate\Http\Resources\Json\JsonResource;

class OpeningHoursResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $days = [
            'saturday'      =>  'Samstag',
            'sunday'        =>  'Sonntag',
            'monday'        =>  'Montag',
            'tuesday'       =>  'Dienstag',
            'wednesday'     =>  'Mittwoch',
            'thursday'      =>  'Donnerstag',
            'friday'        =>  'Freitag'
        ];
        return [
            'day'                   =>  $days[$this->day],
            'first_start_time'      =>  $this->first_start_time,
            'first_end_time'        =>  $this->first_end_time,
            'last_start_time'       =>  $this->last_start_time,
            'last_end_time'         =>  $this->last_end_time,
        ];
    }
}
