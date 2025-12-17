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
}
