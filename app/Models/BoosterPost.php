<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoosterPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'booster_id',
        'sales_partner_id',
        'title',
        'body',
        'file',
        'file_name',
        'range',
        'posting_date',
        'posting_time'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($booster_post){
            $booster_post->prefix_id = nextId('booster_posts', self::PREFIX);
        });
    }

    public const PREFIX = 'BP';


    public function salesPartner()
    {
        return $this->belongsTo(SalesPartner::class, 'sales_partner_id');
    }


    public function getFileUrlAttribute()
    {
        if (is_null($this->file)) {
            return null;
        }

        if (preg_match("@http@", $this->file)) {
            return $this->file;
        }

        if(config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale' ){
            return Storage::disk(env('FILESYSTEM_DISK'))->publicUrl($this->file);
        }

        return Storage::url($this->file);
    }


    public function likes()
    {
        return $this->hasMany(Like::class, 'story_id')
        ->where('post_type', 'booster_post')
        ;
    }

    public function scopeIsLike($query, $user)
    {
        $query->addSelect(['like_by_me'=>Like::select(['id'])
            ->where('mobile_app_user_id', $user->id)
            ->whereColumn('story_id', 'booster_posts.id')
            // ->latest()
            ->where('post_type', 'booster_post')
            ->take(1)
        ]);
    }



    public function comments()
    {
        return $this->hasMany(Comment::class, 'story_id')
            ->where('comment_type', 'booster_post')
            ;
    }
}
