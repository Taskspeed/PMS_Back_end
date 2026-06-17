<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOfficeAssign extends Model
{
    //

    protected $table = 'user_office_assigns';



    protected $fillable = [
        'user_id',
        'office_id',
        'assigned_by',
        'office_id_assign'
    ];
// UserOfficeAssign model
protected $casts = [
    'user_id'  => 'integer',
    'office_id' => 'integer',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
