<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoActivity extends Model
{
    use HasFactory;

    protected $table = 'video_activity';

 	public function video()
    {
        return $this->belongsTo('App\Models\Video','video_id','id');
    }
}
