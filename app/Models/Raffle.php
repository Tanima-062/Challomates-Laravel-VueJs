<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Raffle extends Model
{
    use HasFactory;

    const PREFIX = 'VL';

    protected $fillable = [
        'prefix_id',
        'started_at',
        'stopped_at',
        'winning_number',
        'sweepstake_id',
        'video_src_path',
        'playback_url'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($raffle) {
            $raffle->prefix_id = nextId('raffles', self::PREFIX);
        });
    }
}
