<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    //
    use LogsActivity, SoftDeletes;
    
    protected $fillable =[

        'name',
        'position',
        'rank',
        'office',
        'division',
        'section',
        'unit',
        'office_id',
    ];

    protected $casts = [
        'office_id' => 'integer',

    ];
    public function office()
    {
        return $this->belongsTo(office::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'position', 'rank', 'office', 'division', 'section', 'unit', 'office_id'])
            ->setDescriptionForEvent(fn(string $eventName) => "Employee has been {$eventName}")
            ->useLogName('Employee')
            ->logOnlyDirty();
    }
}
