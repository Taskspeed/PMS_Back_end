<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technical extends Model
{
    /** @use HasFactory<\Database\Factories\TechnicalFactory> */
    use HasFactory;

    protected $fillable = [
        'Technical Knowledge and Skills',
        'Analytical Thinking',
        'Problem Solving',
        'Decision Making',
        'Innovation and Creativity',
        'Technical Expertise',
    ];


    protected $casts = [
        'Technical Knowledge and Skills' => 'integer',
        'Analytical Thinking' => 'integer',
        'Problem Solving' => 'integer',
        'Decision Making' => 'integer',
        'Innovation and Creativity' => 'integer',
        'Technical Expertise' => 'integer',
    ];
    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
