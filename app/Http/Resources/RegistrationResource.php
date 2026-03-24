<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'match' => new FootballMatchResource($this->whenLoaded('match')),
        ];
    }
}
