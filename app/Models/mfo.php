<?php

namespace App\Models;

use App\Models\office;
use Illuminate\Database\Eloquent\Model;

class mfo extends Model
{
    //
     protected $fillable =[
        'office_id',
        'name',
        'category'
     ];

    // Define relationship with Office
    public function office()
    {
        return $this->belongsTo(office::class);
    }
}
