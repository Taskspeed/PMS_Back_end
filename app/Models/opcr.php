<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class opcr extends Model
{
    //
    protected $fillable=[

        'employee_id',
        'target_period',
        'year',
        'strategic function',
        'core function',
        'support function',
        'core',
        'technical',
        'leadership',
        'alloted budget',
        'actual accomplishment',
        'rating_q',
        'rating_e',
        'rating_t',
         'rating_a',
         'profiency result',
         'remarks',
         'office_id'

    ];

    protected $casts = [
        'office_id' => 'integer',
        'employee_id' => 'integer',

    ];

}
