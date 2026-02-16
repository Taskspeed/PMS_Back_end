<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetenciesAttitude extends Model
{
    //


    protected $table = 'competencies_attitudes';
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
