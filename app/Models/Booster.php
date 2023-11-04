<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booster extends Model
{
    use HasFactory;
    protected $fillable = [
        'sales_partner_id',
        'contract_id',
        'title',
        'range',
        'status',
        'picture',
        'type'
    ];

    protected $casts = [
        // 'posting_time' => 'datetime',
        'start' => 'date',
        'end' => 'date',
    ];



    public function boosterBoosterTypes():HasMany
    {
        return $this->hasMany(BoosterBoosterType::class, 'booster_id');
    }
    public function salesPartner():BelongsTo
    {
        return $this->belongsTo(SalesPartner::class, 'sales_partner_id');
    }
    public function contract():BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function($booster){
            $booster->prefix_id = nextId('boosters', self::PREFIX);
        });
    }
    public const PREFIX = 'B';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public $with = ['salesPartner','contract', 'boosterBoosterTypes'];


    public function getFileUrlAttribute()
    {
        if (is_null($this->file)) {
            return asset("images/image.png");
        }

        if (preg_match("@http@", $this->file)) {
            return $this->picture;
        }

        if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
            return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->file);
        }

        return Storage::disk(env('FILESYSTEM_DISK'))->url($this->file);
    }

}
