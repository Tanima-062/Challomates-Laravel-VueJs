<?php

namespace App\Http\Resources\Coin;

use Illuminate\Http\Resources\Json\JsonResource;

class CoinDebitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $description = $this->type == 'sweepstake' ? 'Jackpot- und Zusatz-Gewinnspiel Teilnahme' : 'Jackpot-Gewinnspiel-Teilnahme';

        return  [
            'id'                =>  $this->participation_id,
            'description'       =>  $description,
            'number_of_entries' =>  $this->count_participation,
            'coins_per_entry'   =>  $this->number_of_coins_for_participation,
            'total_coins'       =>  (string) number_format(($this->number_of_coins_for_participation * $this->count_participation), 2, '.', ''),
            'created_at'        =>  $this->participations_created_at,
            'sweepstake'    =>  [
                'id'        =>  $this->id,
                'name'        =>  $this->name,
            ]
        ];
    }
}
