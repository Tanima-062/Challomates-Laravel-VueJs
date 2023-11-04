<?php

namespace App\Http\Resources\MobileAppUser;

use App\Models\Follower;
use App\Models\MobileAppUser;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Language\LanguageResource;

class MobileAppUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $user = request()->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));
        // $totalFollowings = $this->AcceptedFollowings()
        //         ->UserFollowings()
        //         ->count();

        // $totalFollowers = $this->AcceptedFollowers()
        //     ->UserFollowers()
        //     ->count()
        // ;

        // $totalPendingFollowers = $this->PendingFollowers()
        // ->UserFollowers()
        // ->count()
        // ;

        // $totalFollowings = Follower::UserFollowings()
        //  ->accept()
        //  ->WhereFollower($this->id)
        //  ->where('followable_type', MobileAppUser::class)
        //  ->whereHas('followerable', function($q){
        //      $q->where('status', MobileAppUser::STATUS_ACTIVE);
        //  })
        //  ->count()
        //  ;

        $totalFollowings = Follower::UserFollowings()
            ->WhereFollower($this->id)
            ->whereNotIn('followable_id', $blocked_users_id)
            ->whereHas('followable', function($q){
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->accept()
            ->count()
        ;

        $totalFollowers = Follower::UserFollowings()
        //  ->pending()
         ->WhereFollowabble($this->id)
         ->whereNotIn('follower_id', $blocked_users_id)
         ->whereHas('followerable', function($q){
             $q->where('status', MobileAppUser::STATUS_ACTIVE);
         })
         ->accept()
         ->count()
         ;

        $totalPendingFollowers = Follower::UserFollowings()
         ->pending()
         ->WhereFollowabble($this->id)
         ->whereNotIn('follower_id', $blocked_users_id)
         ->whereHas('followerable', function($q){
             $q->where('status', MobileAppUser::STATUS_ACTIVE);
         })
         ->count()
         ;

        return [
            'id'                => $this->id,
            'prefix_id'         => $this->prefix_id,
            'photo_url'         => $this->photo_url,
            'username'          => $this->username,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'name'              => $this->name,
            'date_of_birth'     =>  $this->date_of_birth->format('d.m.Y'),
            'email'             => $this->email,
            'street'            => $this->street,
            'house_number'      => $this->house_number,
            'zip_code'          => $this->zip_code,
            'city'              => $this->city,
            'country'           => $this->country,
            'country_iso_code'  => $this->country_iso_code,
            'phone_number'      => $this->phone_number,
            'full_phone_number' => $this->full_phone_number,
            'type'              => $this->type,
            'privacy'           => $this->privacy,
            'language_id'       => $this->language_id,
            'status'            => $this->status,
            'coin'              => (string) $this->coin,
            'created_at'        => $this->created_at,
            'language'          =>  $this->whenLoaded('language', fn()=> new LanguageResource($this->language)),
            'followed_by_me_count'    =>  $totalFollowings,
            'followed_by_mate_count'  =>  $totalFollowers,
            'follower_request_count'  =>  $totalPendingFollowers,
        ];
    }
}
