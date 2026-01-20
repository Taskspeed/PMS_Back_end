<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceConfigurations extends Model
{
    //

    protected $table = 'performance_configurations';

    protected $fillable = [
        'performance_standard_id',
        'target_output',
        'quantity_indicator',
        'timeliness_indicator',
        
        'timeliness_range',
        'timeliness_date',
        'timeliness_description',

    ];
    public function performanceStandard()
    {
        return $this->belongsTo(PerformanceStandard::class);
    }
}
