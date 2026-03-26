<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'skill_level' => $this->skill_level,
            'favourite_position' => $this->favourite_position,
            'activity_score' => $this->getActivityScore(),
            'rank' => $this->getRank(),
            'role' => $this->getRoleNames()->first(),
        ];
    }
}
