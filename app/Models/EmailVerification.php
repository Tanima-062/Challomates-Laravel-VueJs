<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EmailVerification extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'mobile_email_verification';
    protected $fillable = ['email', 'mobile_app_user_id', 'token'];

}
