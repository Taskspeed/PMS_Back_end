<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class F_outpot extends Model
{
   use LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'mfo_id', 'f_category_id', 'office_id'];

    // Define relationship with MFO
    public function mfo()
    {
        return $this->belongsTo(Mfo::class);
    }

    // Define the relationship
    public function category()
    {
        return $this->belongsTo(F_category::class, 'f_category_id');
    }
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }


    protected $casts = [
        'mfo_id' => 'integer',
        'f_category_id' => 'integer',
        'office_id' => 'integer',

    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([ 'name', 'mfo_id'])
            ->setDescriptionForEvent(fn(string $eventName) => "Output has been {$eventName}")
            ->useLogName('output');
    }
}
