<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeStatus extends Model
{
    //

    protected $table = 'employee_status';

    protected $fillable = [
        'year',
        'semester',
        'consultant',
        'regular',
        'casual',
        'contractual',
        'elective',
        'co-terminous',
        'temporary',
        'not Known',
        'LSB',
        'probationary',
        'substitute',
        'appointed',
        'job order',
        're-elect',
        'emergency',
        'honorarium',
        'permanent',
        'provisional',
        'total_employee',
    ];
}
