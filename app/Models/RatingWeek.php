<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingWeek extends Model
{
    //
    protected $table = 'rating_weeks';

    protected $fillable = [
        'target_period_id',
        'week',
        'status'
    ];
}
