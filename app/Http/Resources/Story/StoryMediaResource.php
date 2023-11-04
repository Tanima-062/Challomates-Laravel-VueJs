<?php

namespace App\Http\Resources\Story;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryMediaResource extends JsonResource
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
            'story_id'      =>  $this->story_id,
            'media_type'    =>  $this->media_type,
            'media_url'     =>  $this->media_url
        ];
    }
}
