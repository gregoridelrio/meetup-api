<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('Admin1234'),
            'phone' => '666666661',
            'skill_level' => 'advanced',
            'favourite_position' => 'midfielder',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $player1 = User::create([
            'name' => 'Pepito',
            'email' => 'pepito@email.com',
            'password' => Hash::make('Password1234'),
            'phone' => '666666662',
            'skill_level' => 'beginner',
            'favourite_position' => 'striker',
            'role' => 'player',
        ]);
        $player1->assignRole('player');

        $player2 = User::create([
            'name' => 'Juanito',
            'email' => 'juanito@email.com',
            'password' => Hash::make('Password431'),
            'phone' => '666666663',
            'skill_level' => 'intermediate',
            'favourite_position' => 'goalkeeper',
            'role' => 'player',
        ]);
        $player2->assignRole('player');
    }
}
