<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FootballMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'starts_at' => $this->starts_at,
            'duration' => $this->duration,
            'match_type' => $this->match_type,
            'max_players' => $this->max_players,
            'required_level' => $this->required_level,
            'price' => $this->price,
            'location_name' => $this->location_name,
            'address' => $this->address,
            'city' => $this->city,
            'status' => $this->status,
            'organizer' => new UserResource($this->whenLoaded('organizer')),
            'registrations' => RegistrationResource::collection($this->whenLoaded('registrations')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
