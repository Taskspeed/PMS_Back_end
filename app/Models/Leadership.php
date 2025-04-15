<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leadership extends Model
{
    /** @use HasFactory<\Database\Factories\LeadershipFactory> */
    use HasFactory;

    protected $fillable = [
        'Thinking Strategically and Creatively',
        'Problem Solving and Decision Making',
        'Building Collaborative & Inclusive Working Relationships',
        'Managing Performance & Coaching for Results',

    ];

    protected $casts = [
        'Thinking Strategically and Creatively' => 'integer',
        'Problem Solving and Decision Making' => 'integer',
        'Building Collaborative & Inclusive Working Relationships' => 'integer',
        'Managing Performance & Coaching for Results' => 'integer',
    ];
    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
