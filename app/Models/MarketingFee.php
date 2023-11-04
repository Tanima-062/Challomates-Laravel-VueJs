<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingFee extends Model
{
    use HasFactory;
    protected $fillable = [
        'designation',
        'marketing_fee',
        'direct_consumers_senior_partner_share',
        'direct_consumers_share_jackpot',
        'direct_consumers_share_fee_challomates',
        'direct_consumers_share_challomates_marketing_ag',
        'distribution_consumers_share_of_consultants',
        'distribution_consumers_proportion_of_sales_partners',
        'distribution_consumers_share_jackpot',
        'distribution_consumers_share_challomates_marketing_ag'
    ];


    public const PREFIX = 'MG';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';


    public static function boot()
    {
        parent::boot();

        static::creating(function($marketing_fee){
            $marketing_fee->prefix_id = nextId('marketing_fees', self::PREFIX);
        });
    }
}
