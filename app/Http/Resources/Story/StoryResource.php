<?php

namespace App\Http\Resources\Story;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $request->user();

        return [
            'id'            =>  $this->id,
            'store_id'            =>  $this->sales_partner_id,
            'mobile_app_user_id'            =>  $this->mobile_app_user_id,
            'check_in_id'            =>  $this->check_in_id,
            'title'         =>  $this->title,
            'created_at'    =>  $this->created_at,
            'is_own_story'  =>  $this->mobile_app_user_id === $user->id,
            "comments_count"=>  $this->comments_count,
            "likes_count"   =>  $this->likes_count,
            "like_by_me"    =>  $this->like_by_me ? true: false,
            'tagged_me'     =>  $this->tagged_me ? true: false,
            'media'         =>  $this->whenLoaded('media', function(){
                return  StoryMediaResource::collection($this->media);
            }),
            'creator'       =>  $this->whenLoaded('creator', function(){
                return  new StoryCreatorResource($this->creator);
            }),
            'tagged'    =>  $this->whenLoaded('tagged', function(){
                return  TaggedUserResource::collection($this->tagged);
            })
        ];
    }
}
