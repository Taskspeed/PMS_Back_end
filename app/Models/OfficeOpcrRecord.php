<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeOpcrRecord extends Model
{
    //

    protected $table = 'office_opcrs_records';


    protected $fillable = [

        'office_opcr_id',
        'date',
        'status',
        'remarks',
        'processed_by',
        'processed_by_name'

    ];

    public function officeOpcr()
    {
        return $this->belongsTo(OfficeOpcr::class);
    }
}
