<?php

namespace App\Models;

use App\Models\mfo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class office extends Model
{
     protected $fillable =['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }


    // Define relationship with Mfo
    public function mfos()
    {
        return $this->hasMany(mfo::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function f_outpot()
    {
        return $this->hasMany(f_outpot::class,);
    }
    public function unit_work_plan()
    {
        return $this->hasMany(Unit_work_plan::class);
    }
}
