<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceRatingAttachment extends Model
{
    //
    protected $table = 'performance_rating_attachments';

     protected $fillable = [
        'performance_standard_id',
        'week_number',
        'month',
        'year',
        'file_path',
        'original_name',
    ];
}
