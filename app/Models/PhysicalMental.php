<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalMental extends Model
{
    //



    protected $table = 'physical_mentals';
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
