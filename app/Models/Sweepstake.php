<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sweepstake extends Model
{
    use HasFactory;

    protected $fillable = [
        'challomates_admin_id',
        'prefix_id',
        'name', 'type',
        'runtime_from',
        'runtime_to',
        'raffle_time',
        'price',
        'number_of_winners',
        'total_sweepstake_number_position',
        'winning_number_position_from',
        'winning_number_position_to',
        'number_of_coins_for_participation',
        'status',
        'publish_status',
    ];

    CONST PREFIX = 'GS';

    public static function boot()
    {
        parent::boot();

        static::creating(function($sweepstake){
            $sweepstake->prefix_id = nextId('sweepstakes', self::PREFIX);
        });
    }


    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function scopeActiveNotCancel($query)
    {
        $query->where(function($q){
            $q->where(function ($q) {
                $q->where('publish_status', 'published')
                    ->where('status',  '!=', 'canceled')
                    ;
            })
            ->orWhereNull('status');
        })
        ->where('publish_status', '!=', 'not_published')
        ;
    }

    public function scopeTimeBetween($query, string $column = 'created_at')
    {
        $start_date = Carbon::parse(request('start_date'))->startOfDay();
        $end_date = Carbon::parse(request('end_date'))->endOfDay();

        $query->where($column, '>=',$start_date)
            ->where($column, '<=', $end_date)
        ;
    }
}
