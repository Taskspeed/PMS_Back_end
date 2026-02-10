<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    //
    protected $table = 'months';

    protected $fillable = [
        'target_period_id',
        'month',
    ];

    public function targetPeriod()
    {
        return $this->belongsTo(TargetPeriod::class, 'target_period_id');
    }

    public function absents()
    {
        return $this->hasMany(Absent::class, 'month_id');
    }
    
    public function lates()
    {
        return $this->hasMany(Late::class, 'month_id');
    }

}
