<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'organizer_id',
        'description',
        'starts_at',
        'duration',
        'match_type',
        'max_players',
        'required_level',
        'price',
        'location_name',
        'address',
        'city',
        'status',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'match_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'match_id');
    }
}
