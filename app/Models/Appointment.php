<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = "appointments";

    public function host(){
        return $this->belongsTo('App\Models\Host');
    }

    public function guest(){
        return $this->belongsTo('App\Models\Guest');
    }


    //
}
