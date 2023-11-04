<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_partner_id', 'mobile_app_user_id', 'title', 'check_in_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($story) {
            $story->tagged()->detach();
            $story->media->each->delete();


            $story->likes->each->delete();
            $story->comments->each->delete();
            $story->reports()->detach();
            //
            $story->storyUsers()->detach();
        });
    }


    /**
     * Creator of the stories
     *
     * @return void
     */
    public function creator()
    {
        return $this->belongsTo(MobileAppUser::class, 'mobile_app_user_id', 'id');
    }

    /**
     * Creator of the stories
     *
     * @return void
     */
    public function salesPartner()
    {
        return $this->belongsTo(SalesPartner::class, 'sales_partner_id', 'id');
    }

    /**
     * Store visits of the stories
     *
     * @return void
     */
    public function checkIn()
    {
        return $this->belongsTo(StoreVisits::class, 'check_in_id', 'id');
    }


    public function media()
    {
        return $this->hasMany(StoryMedia::class);
    }


    public function tagged()
    {
        return $this->belongsToMany(MobileAppUser::class, 'story_tag', 'story_id', 'mobile_app_user_id');
    }

    public function reports()
    {
        return $this->belongsToMany(MobileAppUser::class, 'story_user_report', 'story_id', 'mobile_app_user_id');
    }

    public function storyUsers()
    {
        return $this->belongsToMany(MobileAppUser::class, 'story_user', 'story_id', 'mobile_app_user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'story_id')
            ->where('post_type', 'post');
    }

    public function scopeIsLike($query, $user)
    {
        $query->addSelect([
            'like_by_me' => Like::select(['id'])
                ->where('mobile_app_user_id', $user->id)
                ->whereColumn('story_id', 'stories.id')
                // ->latest()
                ->where('post_type', 'post')
                ->take(1)
        ]);
    }
    public function scopeIsTagged($query, $user)
    {
        $query->addSelect([
            'tagged_me' => StoryTag::select(['id'])
                ->where('mobile_app_user_id', $user->id)
                ->whereColumn('story_id', 'stories.id')
                // ->latest()
                ->take(1)
        ]);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'story_id')
            ->where('comment_type', 'post');
    }

    public function scopeExcludeReportedPosts($q, $userId)
    {
        return $q->whereDoesntHave('reports', fn ($query) => $query->where('mobile_app_users.id', $userId));
    }
}
