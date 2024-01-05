<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course' => $this->course,
            'name' => $this->name,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'file_url' => $this->getFirstMedia('videos')?->getUrl(),
            'file_path' => $this->getFirstMedia('videos')?->getPath(),
            'file_name' => $this->getFirstMedia('videos')?->name,
        ];
    }
}
