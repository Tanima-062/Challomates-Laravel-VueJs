<?php

namespace App\Http\Resources\Story;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->id,
            'body'          =>  $this->body,
            'user'          =>  $this->whenLoaded('user', function(){
                $user = $this->user;
                return [
                    'id'                =>  $user->id,
                    'username'          =>  $user->username,
                    'first_name'        =>  $user->first_name,
                    'last_name'         =>  $user->last_name,
                    'name'              =>  $user->name,
                    'photo_url'         =>  $user->photo_url,
                ];
            }),
            'created_at'    =>  $this->created_at,
        ];
    }
}
