<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandardOutcome extends Model
{
    //
    protected $table = 'standard_outcomes';


    protected $fillable = [
        'target_period_id',
        'rating',
        'quantity_target',
        'effectiveness_criteria',
        'timeliness_range',


    ];
    public function targetPeriod()
    {
        return $this->belongsTo(TargetPeriod::class);
    }

}
