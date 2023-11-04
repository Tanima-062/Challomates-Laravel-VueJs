<?php

namespace App\Http\Resources\SweepStakes;

use Illuminate\Http\Resources\Json\JsonResource;

class SweepStakeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = request()->user();

        $sweepstake = $this;
        $insideRuntime = $sweepstake->runtime_from <=  now() && $sweepstake->runtime_to >= now();

        $status = ($sweepstake->status == 'drawn' ? 'Ausgelost' : ($sweepstake->runtime_from <=  now() && $sweepstake->runtime_to >= now() ?  'Aktiv' : 'Teilnahmeschluss vorbei' ));

        $can_reedem = (($status == 'Aktiv') &&  ($user->coin >= $this->number_of_coins_for_participation)) ? true : false;

        return [
            'id'                =>  $this->id,
            'prefix_id'         =>  $this->prefix_id,
            'name'              =>  $this->name,
            'price'             =>  $this->price,
            // 'price'             =>  number_format((float) $this->price, '2', '.', ''),
            // 'price'             =>  number_format((float) $this->price, '2', '.', ''),

            'total_draw_paying_stations'  =>  $this->total_sweepstake_number_position,
            'number_of_coins_for_participation'             =>  $this->number_of_coins_for_participation,
            'runtime_from'              =>  $this->runtime_from,
            'runtime_to'                =>  $this->runtime_to,
            'raffle_time'               =>  $this->raffle_time,
            // 'status'                    =>  $this->status,
            'status'                    =>  $status,
            'created_at'                =>  $this->created_at,
            // 'can_redeem'                =>  ($user->coin >= $this->number_of_coins_for_participation) ? true : false,
            'can_redeem'                =>  $can_reedem,
        ];
    }
}
