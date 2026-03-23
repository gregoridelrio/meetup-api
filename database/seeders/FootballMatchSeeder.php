<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\User;
use Illuminate\Database\Seeder;

class FootballMatchSeeder extends Seeder
{

    public function run(): void
    {
        $player1 = User::where('email', 'pepito@email.com')->first();
        $player2 = User::where('email', 'juanito@email.com')->first();

        FootballMatch::create([
            'organizer_id' => $player1->id,
            'description' => 'Partido de futbol 7 en Barcelona',
            'starts_at' => '2026-05-01 18:00:00',
            'duration' => 90,
            'match_type' => '7v7',
            'max_players' => 14,
            'required_level' => 'beginner',
            'price' => 5.00,
            'location_name' => 'Camp Municipal',
            'address' => 'Carrer Gran Via 1',
            'city' => 'Barcelona',
            'status' => 'open',
        ]);

        FootballMatch::create([
            'organizer_id' => $player2->id,
            'description' => 'Partido de futbol 11 en Barcelona',
            'starts_at' => '2026-05-05 17:00:00',
            'duration' => 60,
            'match_type' => '11v11',
            'max_players' => 22,
            'required_level' => 'advanced',
            'price' => 0,
            'location_name' => 'Estadi Municipal',
            'address' => 'Carrer Gran Via 2',
            'city' => 'Barcelona',
            'status' => 'open',
        ]);
    }
}
