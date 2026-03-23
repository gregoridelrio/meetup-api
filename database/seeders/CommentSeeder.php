<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\FootballMatch;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $player1 = User::where('email', 'pepito@email.com')->first();
        $player2 = User::where('email', 'juanito@email.com')->first();

        $match1 = FootballMatch::where('description', 'Partido de futbol 7 en Barcelona')->first();
        $match2 = FootballMatch::where('description', 'Partido de futbol 11 en Barcelona')->first();

        Comment::create([
            'match_id' => $match1->id,
            'user_id' => $player2->id,
            'content' => 'Me apunto!',
        ]);

        Comment::create([
            'match_id' => $match2->id,
            'user_id' => $player1->id,
            'content' => 'Voy!',
        ]);
    }
}
