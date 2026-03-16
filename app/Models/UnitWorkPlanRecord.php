<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitWorkPlanRecord extends Model
{
    //

    protected $table = 'unitworkplan_records';


    protected $fillable = [
        'office_name',
        'semester',
        'year',
        'status',
        'reviewed_by',
    ];
}
