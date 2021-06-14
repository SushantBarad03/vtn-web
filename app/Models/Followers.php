<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followers extends Model
{
    use HasFactory;

    protected $table = 'followers';

   	public function user()
    {
        return $this->belongsTo('App\Models\User','id','user_id');
    }

	public function users()
    {
        return $this->hasMany('App\Models\User','id','followers');
    } 

    public function userfollowing()
    {
        return $this->hasMany('App\Models\User','id','user_id');
    }    
}
