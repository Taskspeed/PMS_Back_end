<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class F_outpot extends Model
{
   use LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'mfo_id'];

    // Define relationship with MFO
    public function mfo()
    {
        return $this->belongsTo(Mfo::class);
    }

    protected $casts = [
        'mfo_id' => 'integer',

    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([ 'name', 'mfo_id'])
            ->setDescriptionForEvent(fn(string $eventName) => "Output has been {$eventName}")
            ->useLogName('output');
    }
}
