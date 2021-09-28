<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    //
    protected $table = "tokens";

    protected $fillable = [
        'token','type'
    ];

    public function user(){
        return $this->hasMany('App\Models\User');
    }
}
