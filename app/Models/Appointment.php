<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = "appointments";
    protected $fillable=['host_id','guest_id','status','notes','purpose','date','time'];

    public function host(){
        return $this->belongsTo('App\Models\Host', 'host_id');
    }

    public function guest(){
        return $this->belongsTo('App\Models\Guest', 'guest_id');
    }
    //
}
