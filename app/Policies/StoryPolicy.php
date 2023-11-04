<?php

namespace App\Policies;

use App\Models\MobileAppUser;
use App\Models\Story;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Story $story)
    {
        //
    }



    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(MobileAppUser $mobileAppUser, Story $story)
    {
        return $story->mobile_app_user_id == $mobileAppUser->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Story  $story
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(MobileAppUser $mobileAppUser, Story $story)
    {
        return $story->mobile_app_user_id == $mobileAppUser->id;
    }

}
