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

    public function configurations()
    {
        return $this->hasMany(Configuration::class, 'performance_standard_id');
    }
}
