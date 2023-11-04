<?php

namespace App\Http\Resources\SweepStakes;

use Illuminate\Http\Resources\Json\JsonResource;

class MyParticipationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $sweepstake = $this->sweepstake;

        $status = ($sweepstake->status == 'drawn' ? 'Ausgelost' : ($sweepstake->runtime_from <=  now() && $sweepstake->runtime_to >= now() ?  'Aktiv' : 'Teilnahmeschluss vorbei' ));

        // $status = ($sweepstake->status == 'drawn' ? 'Ausgelost' : ($sweepstake->runtime_to > now() ? 'Teilnahmeschluss vorbei' : 'Aktiv' ));
        return [
            'id'                    =>  $this->participation_id,
            'created_at'            =>  $this->created_at,
            'raffle'                =>  $sweepstake->name,
            'winning_number'        =>  join('', explode('-', $this->winning_number)),
            'draw_date'             =>  $sweepstake->raffle_time,
            // 'sweepstake_status'     =>  $sweepstake->status,
            'sweepstake_status'     =>  $status,
        ];
    }
}
