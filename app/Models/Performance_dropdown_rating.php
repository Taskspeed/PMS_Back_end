<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Performance_dropdown_rating extends Model
{
    //

    protected $table = 'Performance_dropdown_ratings';


    protected $fillable = [
        'performance_rating_id',
        'quantity',
        'effectiveness',
        'timeliness'
    ];

    public function performanceRating()
    {
        return $this->belongsTo(PerformanceRating::class);
    }
}
