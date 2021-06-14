<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';
    protected $softDelete = true;

   	public function users()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    public function uservideo()
    {
        return $this->hasMany('App\Models\UserVideo','id','video_id');
    }

    public function videoactivity()
	{
		return $this->hasMany('App\Models\VideoActivity','video_id','id');
	}

    public function hashtag()
    {
        return $this->hasMany('App\Models\Hashtag','id','video_id');
    }

    public function report()
    {
        return $this->hasMany('App\Models\Report');
    }
}
