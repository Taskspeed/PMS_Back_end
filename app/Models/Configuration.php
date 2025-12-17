<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    //

    protected $table = 'configurations';

    protected $fillable = [
        'performance_standard_id',
        'quantity',
        'timeliness',
        'type',
    ];
    public function performanceStandard()
    {
        return $this->belongsTo(PerformanceStandard::class);
    }
}
