<?php

namespace App\Models;

use App\Models\office;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class mfo extends Model
{
 use LogsActivity;
    //
     protected $fillable =[
        'office_id',
        'name',
        'f_category_id' // ✅ Correct
    ];

    protected $casts = [
        'f_category_id' => 'integer',
        'office_id' => 'integer',

    ];
    // Define relationship with Office
    public function office()
    {
        return $this->belongsTo(office::class);
    }

    // Define the relationship
    public function category()
    {
        return $this->belongsTo(F_category::class, 'f_category_id');
    }

    // Relationship with Outpot
    public function outpots()
    {
        return $this->hasMany(F_outpot::class);
    }

    // public function unitWorkPlans()
    // {
    //     return $this->hasMany(Unit_work_plan::class);
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['office_id', 'name', 'category'])
            ->setDescriptionForEvent(fn(string $eventName) => "MFO has been {$eventName}")
            ->useLogName('MFO');
    }
}
