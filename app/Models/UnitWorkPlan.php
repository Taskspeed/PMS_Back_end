<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UnitWorkPlan extends Model
{
    //

    protected  $table = 'unitworkplans';

    protected $fillable = [
            'office_name',
            'semester',
            'year'

    ];

    // protected $casts = [
    //     'id'             => 'integer',
    //     'unitworkplan_id' => 'integer', // ✅ cast the alias too
    // ];
    public function unitworkplanRecord()
    {
        return $this->hasMany(UnitWorkPlanRecord::class, 'unitworkplan_id');
    }

    // lastest record
    // public function latestUnitworkplanRecord()
    // {
    //     return $this->hasOne(UnitWorkPlanRecord::class, 'unitworkplan_id')->latestOfMany();
    // }

    public function unitworkplanLastestRecord()
    {
        return $this->hasOne(UnitWorkPlanRecord::class, 'unitworkplan_id')->latestOfMany();
    }
}
