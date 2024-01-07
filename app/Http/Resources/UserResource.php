<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'phone_number' => $this->phone_number,
            'roles' => $this->roles()->without('permissions')->get(),
            'device' => $this->when(auth()->user()->hasRole('super-admin'), $this->device),
            'image_url' => $this->getFirstMedia('images')?->getUrl(),
            'image_path' => $this->getFirstMedia('images')?->getPath(),
            'image_name' => $this->getFirstMedia('images')?->name,
        ];
    }
}
