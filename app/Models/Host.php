<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Host extends Model
{

    protected $table = "hosts";
    protected $fillable=['name','nip','position','user_id'];

    public function appointments(){
        return $this->hasMany('App\Models\Appointment');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    //
}
