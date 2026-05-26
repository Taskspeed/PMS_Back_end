<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XPersonal extends Model
{
    //


    // protected $connection = 'second_db'; 
    protected $table = 'xPersonal';

    public function targetPeriods()
    {
        return $this->hasMany(TargetPeriod::class, 'control_no', 'ControlNo');
    }
}
