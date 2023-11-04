<?php

namespace App\Traits;

use App\Models\Follower;

trait SalesPartnerFollower {


    public function mobileFollowers()
    {
        return $this->morphMany(Follower::class, 'followable');
    }
}
