<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    //
    use LogsActivity;

    protected $table = 'employees';

    protected $fillable =[
        'name',
        'rank',
        'office',
        'division',
        'section',
        'unit',
        'position_id',
        'office_id',
        'ControlNo',
        'group',
        'office2',
        'tblStructureID',
        'sg',
        'level',
        'positionID',
        'itemNo',
        'pageNo',
        'position',
        'status'

    ];

    protected $casts = [
        'office_id' => 'integer',
        // 'position_id' => 'integer',

    ];
    public function office()
    {
        return $this->belongsTo(office::class);
    }
    public function position()
    {
        return $this->belongsTo(position::class);
    }
    public function targetPeriods()
    {
        return $this->hasMany(TargetPeriod::class, 'control_no', 'ControlNo');
    }

    // public function unitWorkPlans()
    // {
    //     return $this->hasMany(Unit_work_plan::class);
    // }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'position', 'rank', 'office', 'division', 'section', 'unit', 'office_id'])
            ->setDescriptionForEvent(fn(string $eventName) => "Employee has been {$eventName}")
            ->useLogName('Employee')
            ->logOnlyDirty();
    }

    // public function performanceStandards()
    // {
    //     return $this->hasManyThrough(
    //         PerformanceStandard::class,
    //         TargetPeriod::class,
    //         'control_no',        // FK on target_periods
    //         'target_period_id',  // FK on performance_standards
    //         'ControlNo',         // Local key on employees
    //         'id'                 // Local key on target_periods
    //     );
    // }
}
