<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class opcr extends Model
{
    //

    protected  $table = 'opcrs';

    protected $fillable = [

        'office_id',
        'performance_standard_id',
        'competency',
        'budget',
        'accountable',
        'accomplishment',
        'rating_q',
        'rating_e',
        'rating_t',
        'rating_a',
        'profiency',
        'remarks',

    ];

    // protected $casts = [
    //     'office_id' => 'integer',
    //     'employee_id' => 'integer',

    // ];
    protected $casts = [
        'compentency' => 'array',
        'profiency'   => 'array',
    ];

    public function performanceStandard()
    {
        return $this->belongsTo(performanceStandard::class, 'performance_standard_id');
    }

}
