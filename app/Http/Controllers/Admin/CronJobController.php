<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationDetails;

class CronJobController extends Controller
{
    public function delteNotification(Request $request)
    {
       NotificationDetails::where('status','1')->where('created_at', '<', \Carbon\Carbon::now()->subDays(2))->delete();
       Notification::where('status','1')->where('created_at', '<', \Carbon\Carbon::now()->subDays(2))->delete();       
    }
}
