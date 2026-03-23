<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetPeriodLib extends Model
{
    //


    protected $table = 'target_period_lib';


    protected $fillable = [
        'semester',
        'year',
    ];

    // public function targetPeriodStatus()
    // {
    //     return $this->hasOne(\App\Models\TargetPeriodLock::class, 'semester', 'semester')->whereColumn('target_period_locks.year', 'target_period_lib.year');
    // }

}
