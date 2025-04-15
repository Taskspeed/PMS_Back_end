<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class F_category extends Model
{
    //
    protected $fillable = ['name'];

    // Define the relationship
    public function mfos()
    {
        return $this->hasMany(Mfo::class,);
    }

    public function f_outpot()
    {
        return $this->hasMany(f_outpot::class,);
    }
}
