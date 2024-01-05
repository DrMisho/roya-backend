<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'semester' => $this->semester,
            'academicYear' => $this->academicYear,
            'image_url' => $this->getFirstMedia('images')?->getUrl(),
            'image_path' => $this->getFirstMedia('images')?->getPath(),
            'image_name' => $this->getFirstMedia('images')?->name,
        ];
    }
}
