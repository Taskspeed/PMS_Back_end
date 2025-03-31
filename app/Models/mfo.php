<?php

namespace App\Models;

use App\Models\office;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class mfo extends Model
{
 use LogsActivity;
    //
     protected $fillable =[
        'office_id',
        'name',
        'category'
     ];


    // Define relationship with Office
    public function office()
    {
        return $this->belongsTo(office::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['office_id', 'name', 'category'])
            ->setDescriptionForEvent(fn(string $eventName) => "MFO has been {$eventName}")
            ->useLogName('MFO');
    }
}
