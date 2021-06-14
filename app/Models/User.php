<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    protected $table = 'users';   
    protected $guarded = []; 

    public function video()
	{
		return $this->hasMany('App\Models\Video','id','user_id');
	}

	public function followers()
	{
		return $this->hasMany('App\Models\Followers','user_id','id');
	}

	public function follower()
    {
        return $this->belongsTo('App\Models\User','followers','id');
    }

    // public function videos()
    // {
    //     return $this->belongsTo('App\Models\User','user_id','id');
    // }

    public function uservideo()
    {
        return $this->hasMany('App\Models\UserVideo','parent_user_id','id');
    }

    public function videocomment()
    {
        return $this->belongsTo('App\Models\VideoComment','user_id','id');
    }

    public function report()
    {
        return $this->hasMany('App\Models\Report');
    }
}
