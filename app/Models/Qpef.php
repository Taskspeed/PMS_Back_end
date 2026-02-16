<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qpef extends Model
{
    //



    protected $table = 'qpefs';
    protected $fillable = [

        'control_no',
        'quarterly',
        'year',

    ];


    public function jobPerformances()
    {
        return $this->hasMany(JobPerformance::class);
    }

    public function competenciesAttitudes()
    {
        return $this->hasMany(CompetenciesAttitude::class);
    }

    public function physicalMentals()
    {
        return $this->hasMany(PhysicalMental::class);
    }

    public function recommendationDevelopment()
    {
        return $this->hasOne(RecommendationDevelopment::class);
    }
}
