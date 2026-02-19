<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    //

    protected $table = 'trackers';

    protected $fillable = [
        'office_id',
        'office_name',
        'year',
        'semester',
        'date',
        'status',
        'remarks'
    ];
}
