<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationDetails extends Model
{
    protected $table = 'notification_details';
    protected $softDelete = true;

   	public function notification()
    {
        return $this->hasMany('App\Models\Notification','id','notification_id');
    }
}