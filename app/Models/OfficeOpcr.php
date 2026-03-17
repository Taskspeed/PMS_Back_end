<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeOpcr extends Model
{
    //

    protected $table = 'office_opcrs';

    protected $fillable = [

    'office_id',
    'office_name',
    'semester',
    'year',

    ];

    public function office()
    {
        return $this->belongsTo(office::class);
    }

    public function officeOpcrRecord()
    {
        return $this->hasMany(officeOpcrRecord::class);
    }

    public function officeOpcrRecordLastestRecord()
    {
        return $this->hasOne(OfficeOpcrRecord::class, 'office_opcr_id')->latestOfMany();
    }
}
