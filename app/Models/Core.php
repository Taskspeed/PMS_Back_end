<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Core extends Model
{
    /** @use HasFactory<\Database\Factories\CoreFactory> */
    use HasFactory;

    protected $fillable = [
        'Delivering Service Excellence',
        'Exemplifying Integrity',
        'Interpersonal Skills',
    ];

    protected $casts = [
        'Delivering Service Excellence' => 'integer',
        'Exemplifying Integrity' => 'integer',
        'Interpersonal Skills' => 'integer',
    ];
    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
