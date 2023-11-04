<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoryMedia extends Model
{
    use HasFactory;

    protected $table = 'stories_media';

    protected $fillable = [
        'story_id', 'media_path', 'media_type'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($media){
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($media->media_path);
        });
    }


    public function story()
    {
        return $this->belongsTo(Story::class);
    }


    public function getMediaUrlAttribute()
    {
        if (is_null($this->media_path)) {
            return null;
        }

        if (preg_match("@http@", $this->media_path)) {
            return $this->media_path;
        }

        if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
            return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->media_path);
        }

        return Storage::url($this->media_path);
    }
}
