<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    protected $table = 'followers';

    protected $fillable = [
        'followable_id', 'followable_type', 'follower_id', 'follower_type', 'status', 'accept_at'
    ];

    const ACCEPT = 'accept';
    const REJECT = 'reject';
    const PENDING = 'pending';

    protected $casts = [
        'accept_at'     =>  'datetime'
    ];


    public function followable()
    {
        return $this->morphTo();
    }

    public function followerable()
    {
        return $this->morphTo('follower');
    }


    public function scopeStoreFollowing($query)
    {
        return $query->where('followable_type', SalesPartner::class);
    }


    public function scopeUserFollowings($query)
    {
        return $query->where('followable_type', MobileAppUser::class);
    }


    public function scopeUserFollower($query)
    {
        return $query->where('follower_type', MobileAppUser::class);
    }


    public function scopeWhereFollowabble($query, $id)
    {
        return $query->where('followable_id', $id);
    }
    public function scopeWhereFollowabbleIn($query, array $items = [])
    {
        return $query->whereIn('followable_id', $items);
    }

    public function scopeWhereFollower($query, $id)
    {
        return $query->where('follower_id', $id);
    }


    public function scopeWhereFollowerIn($query, array $items = [])
    {
        return $query->whereIn('follower_id', $items);
    }

    public function scopeAccept($query)
    {
        return $query->where('status', self::ACCEPT);
    }
    public function scopePending($query)
    {
        return $query->where('status', self::PENDING);
    }
}
