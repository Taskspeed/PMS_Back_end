<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitWorkPlanRecord extends Model
{
    //

    protected $table = 'unitworkplan_records';


    protected $fillable = [
        'unitworkplan_id',
          'date',
            'status',
            'remarks',
            'reviewed_by'
    ];


    public function unitworkplan()
    {
        return $this->belongsTo(UnitWorkPlan::class, 'unitworkplan_id');
    }
}
