<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XPersonal extends Model
{
    //

    protected $table = 'xPersonal';

    public function targetPeriods()
    {
        return $this->hasMany(TargetPeriod::class, 'control_no', 'ControlNo');
    }
}
