<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVideo extends Model
{
    use HasFactory;

    protected $table = 'user_video';

	public function video()
    {
        return $this->belongsTo('App\Models\Video','video_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','parent_user_id','id');
    }
}
