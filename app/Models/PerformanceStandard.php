<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceStandard extends Model
{
    //


    protected $table = 'performance_standards';


    protected $fillable = [
        'target_period_id',
        'category',
        'mfo',
        'output',
        'core',
        'technical',
        'leadership',
        'output_name',
        'performance_indicator',
        'success_indicator',
        'required_output',

    ];

    protected $casts = [
        'core' => 'array',
        'technical' => 'array',
        'leadership' => 'array',
    ];

    public function targetPeriod()
    {
        return $this->belongsTo(TargetPeriod::class);
    }
    public function standardOutcomes()
    {
        return $this->hasMany(StandardOutcome::class, 'performance_standard_id');
    }


    public function configurations()
    {
        return $this->hasMany(PerformanceConfigurations::class, 'performance_standard_id');
    }

    public function opcr()
    {
        return $this->hasOne(Opcr::class, 'performance_standard_id');
    }
}
