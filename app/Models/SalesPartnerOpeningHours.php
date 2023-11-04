<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPartnerOpeningHours extends Model
{
    use HasFactory;

    protected $fillable = ['first_start_time', 'first_end_time', 'last_start_time', 'last_end_time', 'sales_partner_id'];

    protected $casts = [
        'first_start_time' => 'datetime:H:i',
        'first_end_time' => 'datetime:H:i',
        'last_start_time' => 'datetime:H:i',
        'last_end_time' => 'datetime:H:i',
    ];
}
