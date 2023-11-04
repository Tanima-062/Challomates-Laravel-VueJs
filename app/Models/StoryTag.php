<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryTag extends Model
{
    use HasFactory;

    protected $table ='story_tag';

    protected $fillable = [
        'story_id', 'mobile_app_user_id', 'creator_id'
    ];
}
