<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table ='states';

     public function city()
    {
        return $this->hasMany('App\Models\City','its_card_number','id');
    }
}
