<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPerformance extends Model
{
    //



    protected $table = 'job_performances';
    protected $fillable = [
        'qpef_id',
        'indicators',
        'rating',
        'remarks'
    ];

    public function qpefs()
    {
        return $this->belongsTo(Qpef::class, 'qpef_id');
    }




}
