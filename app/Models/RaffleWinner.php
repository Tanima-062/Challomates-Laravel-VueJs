<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleWinner extends Model
{
    use HasFactory;

    protected $fillable = array(
        'participation_id',
        'raffle_id',
        'mobile_app_user_id',
        'winning_number',
        'ref_winning_number',
        'winning_position',
    );

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }
}
