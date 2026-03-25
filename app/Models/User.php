<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'skill_level',
        'favourite_position',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organisedMatches()
    {
        return $this->hasMany(FootballMatch::class, 'organizer_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getActivityScore(): int
    {
        $pointsPerOrganizedMatch = 10;
        $pointsPerJoinedMatch = 5;
        $pointsPerComment = 1;

        $matchesOrganized = $this->organisedMatches()->count();
        $matchesJoined = $this->registrations()->count();
        $totalComments = $this->comments()->count();

        return ($matchesOrganized * $pointsPerOrganizedMatch) +
            ($matchesJoined * $pointsPerJoinedMatch) +
            ($totalComments * $pointsPerComment);
    }

    public function getRank(): string
    {
        $activityScore = $this->getActivityScore();

        return match (true) {
            $activityScore >= 100 => 'Legend',
            $activityScore >= 50 => 'Pro',
            $activityScore >= 20 => 'Amateur',
            default => 'Rookie',
        };
    }
}
