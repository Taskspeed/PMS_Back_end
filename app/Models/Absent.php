<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{
    //


    protected $table = 'absents';


    protected $fillable = [
        'month_id',
        'week1',
        'week2',
        'week3',
        'week4',
        'week5',
        'total_absent'
    ];


    public function months()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }
}
