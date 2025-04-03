<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vwplantillastructure extends Model
{
    //

    protected $table = 'vwplantillastructure';

    protected $fillable = [
        'office',
        'office2',
        'Groups',
        'Division',
        'Section',
        'Unit',
        'ItemNo',
        'Position',
        'office_sort',
        'Ordr',
        'groupordr',
        'divordr',
        'secordr',
        'unitordr'
    ];
}
