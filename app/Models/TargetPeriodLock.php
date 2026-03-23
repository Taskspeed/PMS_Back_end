<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetPeriodLock extends Model
{
    //

    protected $table = 'target_period_locks';


    protected $fillable = [

        'semester',
        'year',
        'date',
        'status',
        'lock_by'


    ];


  
}
