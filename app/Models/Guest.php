<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $table = "guests";

    public function appointments(){
        return $this->hasMany('App\Models\Appointment');
    }
    //
}
