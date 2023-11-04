<?php

namespace App\Http\Resources\MobileAppUser;

use App\Models\Follower;
use App\Models\MobileAppUser;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
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


        $totalFollowings = Follower::UserFollowings()
            ->WhereFollower($this->id)
            ->whereHas('followable', function($q){
                $q->where('status', MobileAppUser::STATUS_ACTIVE);
            })
            ->accept()
            ->count()
        ;

        $totalFollowers = Follower::UserFollowings()
        //  ->pending()
         ->WhereFollowabble($this->id)
         ->whereHas('followerable', function($q){
             $q->where('status', MobileAppUser::STATUS_ACTIVE);
         })
         ->accept()
         ->count()
         ;

        $totalPendingFollowers = Follower::UserFollowings()
         ->pending()
         ->WhereFollowabble($this->id)
         ->whereHas('followerable', function($q){
             $q->where('status', MobileAppUser::STATUS_ACTIVE);
         })

         ->count()
         ;


        return [
            'id'                => $this->id,
            'photo_url'         => $this->photo_url,
            'username'          => $this->username,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'name'              => $this->name,
            'privacy'           =>  $this->privacy,
            'followed_by_me_count'    =>  $totalFollowings,
            'followed_by_mate_count'  =>  $totalFollowers,
            'follower_request_count'  =>  $totalPendingFollowers,
            // 'email'             => $this->email,
            // 'street'            => $this->street,
            // 'house_number'      => $this->house_number,
            // 'zip_code'          => $this->zip_code,
            // 'city'              => $this->city,
            // 'country'           => $this->country,
            // 'country_iso_code'  => $this->country_iso_code,
            // 'phone_number'      => $this->phone_number,
            // 'full_phone_number' => $this->full_phone_number,
            // 'type'              => $this->type,
            // 'privacy'           => $this->privacy,
            // 'status'            => $this->status,
            // 'created_at'        => $this->created_at,
        ];
    }
}
