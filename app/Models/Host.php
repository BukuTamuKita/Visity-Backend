<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $table = "hosts";

    public function appointments(){
        return $this->hasMany('App\Models\Appointment');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    //
}
