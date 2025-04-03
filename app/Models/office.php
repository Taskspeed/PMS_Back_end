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
}
