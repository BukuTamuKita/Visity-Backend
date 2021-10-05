<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = "appointments";
    protected $fillable=['hosts_id','guests_id'];

    public function host(){
        return $this->belongsTo('App\Models\Host', 'hosts_id');
    }

    public function guest(){
        return $this->belongsTo('App\Models\Guest', 'guests_id');
    }
    //
}
