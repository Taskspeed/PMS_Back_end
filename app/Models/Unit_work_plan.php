<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Output\Output;

class Unit_work_plan extends Model
{
    //

    // protected $fillable = [
    //     'name',
    //     'rank',
    //     'position',
    //     'division',
    //     'target_period',
    //     'year',
    //     'category',
    //     'mfo',
    //     'output',
    //     'core',
    //     'technical',
    //     'leadership',
    //     'success_indicator',
    //     'required_output',
    //     'employee_id'
    // ];

    // protected $casts = [
    //     'core' => 'array',
    //     'technical' => 'array',
    //     'leadership' => 'array',
    //     'employee_id' => 'integer',
    // ];
    protected $fillable = [
        'office_id',
        'division',
        'target_period',
        'year',
        'employee_id',
        'rank',
        'position',
        'category',
        'mfo',
        'output',
        'core',
        'technical',
        'leadership',
        'success_indicator',
        'required_output',
        'standard_outcomes',
        'status',
         'mode'
    ];

    protected $casts = [
        'core' => 'array',
        'technical' => 'array',
        'leadership' => 'array',
        'standard_outcomes' => 'array',
        'employee_id' => 'integer',
        'year' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
    // In Unit_work_plan.php model
   


}
