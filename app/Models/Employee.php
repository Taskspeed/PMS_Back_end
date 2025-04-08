<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //

    protected $fillable =[

        'name',
        'position',
        'rank',
        'office',
        'division',
        'section',
        'unit',
        'office_id',
    ];

    protected $casts = [
        'office_id' => 'integer',

    ];
    public function office()
    {
        return $this->belongsTo(office::class);
    }
}
