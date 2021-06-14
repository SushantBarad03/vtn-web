<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';
    protected $softDelete = true;

   	public function notificationdetails()
    {
        return $this->belongsTo('App\Models\NotificationDetails','notification_id','id');
    }
}
