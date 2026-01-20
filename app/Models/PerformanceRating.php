<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceRating extends Model
{
    //

    protected $table = 'performance_ratings';


    protected $fillable = [

        'target_period_id',
        'performance_standard_id',
        'control_no',
        'date',
        'quantity_target_rate',
        'effectiveness_criteria_rate',
        'timeliness_range_rate'

    ];
}
