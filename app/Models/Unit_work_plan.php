<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit_work_plan extends Model
{
    //

    protected $fillable = [
        'name',
        'rank',
        'position',
        'division',
        'target_period',
        'year',
        'category',
        'mfo',
        'output',
        'core',
        'technical',
        'leadership',
        'success_indicator',
        'required_output',
        'employee_id'
    ];

    protected $casts = [
        'core' => 'array',
        'technical' => 'array',
        'leadership' => 'array',
        'employee_id' => 'integer',
    ];
}
