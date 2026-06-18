<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetPeriodRecord extends Model
{
    //

    protected $table = 'targetperiod_records';

    protected $fillable = [

        'target_period_id',
        'date',
        'status',
        'remarks',
        'processed_by',
        'processed_by_name',
    ];

    public function ipcr()
    {
        return $this->belongsTo(TargetPeriodRecord::class, 'target_period_id');
    }
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
