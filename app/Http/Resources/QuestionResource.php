<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'semester' => $this->semester,
            'name' => $this->name,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'file_url' => $this->getFirstMedia('files')?->getUrl(),
            'file_path' => $this->getFirstMedia('files')?->getPath(),
            'file_name' => $this->getFirstMedia('files')?->name,
        ];
    }
}
