<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */
    use HasFactory;

    protected $fillable =[
        'name',
        'core_id',
        'technical_id',
        'leadership_id',
    ];

    protected $casts = [
        'core_id' => 'integer',
        'technical_id' => 'integer',
        'leadership_id' => 'integer',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    public function core()
    {
        return $this->belongsTo(Core::class);
    }
    public function technical()
    {
        return $this->belongsTo(Technical::class);
    }
    public function leadership()
    {
        return $this->belongsTo(Leadership::class);
    }

}
