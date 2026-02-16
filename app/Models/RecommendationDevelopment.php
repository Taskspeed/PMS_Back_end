<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationDevelopment extends Model
{
    //


    protected $table = 'recommendation_developments';

    protected $fillable = [
        'qpef_id',
        'for_retention',
        'for_commendation',
        'for_improvement',
        'for_non_renewal',
        'recommendation'
    ];

    public function qpefs()
    {
        return $this->belongsTo(Qpef::class, 'qpef_id');
    }
}
