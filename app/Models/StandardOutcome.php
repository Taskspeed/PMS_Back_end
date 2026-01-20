<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandardOutcome extends Model
{
    //
    protected $table = 'standard_outcomes';


    protected $fillable = [
        'performance_standard_id',
        'rating',
        'quantity_target',
        'effectiveness_criteria',
        'timeliness_range',


    ];
    public function performanceStandards()
    {
        return $this->belongsTo(PerformanceStandard::class);
    }

}
