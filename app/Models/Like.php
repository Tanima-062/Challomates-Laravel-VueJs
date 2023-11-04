<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id', 'mobile_app_user_id', 'type'
    ];

    public function user()
    {
        return $this->belongsTo(MobileAppUser::class, 'mobile_app_user_id');
    }
}
