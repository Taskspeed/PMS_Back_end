<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Late extends Model
{
    //

    protected $table = 'lates';


    protected $fillable = [
        'month_id',
        'week1',
        'week2',
        'week3',
        'week4',
        'week5',
        'total_late'
    ];


    public function months()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }
}
