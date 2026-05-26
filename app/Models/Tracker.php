<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    //

    protected $table = 'trackers';

    protected $fillable = [
        'processed_by',
        'office_name',
        'date',
        'status',
        'remarks',
        'unitworkplan_record_id'
    ];
}
