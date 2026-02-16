<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\Month;
use App\Models\PerformanceStandard;
use App\Models\StandardOutcome;
use Illuminate\Database\Eloquent\Model;

class TargetPeriod extends Model
{
    //

    protected $table = 'target_periods';


    protected $fillable = [
        'control_no',
        'semester',
        'year',
        // 'office',
        // 'office2',
        // 'group',
        // 'division',
        // 'section',
        // 'unit',
        'status'



    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'control_no', 'ControlNo');
    }

    public function performanceStandards()
    {
        return $this->hasMany(PerformanceStandard::class, 'target_period_id');
    }
    public function standardOutcomes()
    {
        return $this->hasMany(StandardOutcome::class, 'target_period_id');
    }

    public function months()
    {
        return $this->hasMany(Month::class, 'target_period_id');
    }

    // public function configurations()
    // {
    //     return $this->hasMany(Configuration::class, 'target_period_id');
    // }
}
