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
            'i' => $this->id,
            'n' => $this->name,
            'e' => $this->email,
            'v' => $this->verified,
            'a' => $this->admin,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }
}
