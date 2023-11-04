<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoosterBoosterType extends Model
{
    use HasFactory;

    protected $fillable = [
        'booster_id',
        'number_of_boosters_month',
        'week',
        'weekday',
        'time'
    ];

    public function booster():BelongsTo
    {
        return $this->belongsTo(Booster::class, 'booster_id');
    }
}
