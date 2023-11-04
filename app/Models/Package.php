<?php

namespace App\Models;

use App\Traits\HasPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory, HasPermission;

    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const PREFIX = 'P';

    protected $table = 'package';

    protected $fillable = array(
        'package_name',
        'services',
        'registration_fee',
        'first_year_fee',
        'yearly_fee',
        'coin_factor',
        'consulting',
        'booster',
        'number_of_registration',
    );


    public static function boot()
    {
        parent::boot();

        static::creating(function($package) {
            $package->package_prefix_id = nextId( 'package', self::PREFIX);
        } );
    }
}
