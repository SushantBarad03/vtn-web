<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationDetails;
use App\Models\Video;

class NotificationController extends Controller
{
    //Get Notification List
    public function getNotification()
    {
    	$notificationData = Notification::with('notificationdetails:id,title,video_id')->where('status','1')->where('user_id',\Auth::user()->id)->get(['notification_id'])->toArray();

        if(!empty($notificationData)){
        	foreach ($notificationData as $key => $value) {                

                $videoData = Video::select('id','user_id','video_name','video_title','views','likes','shares','save','location')
                    ->where('status','1')
                    ->where('id',$value['notificationdetails']['video_id'])
                    ->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture')
                    ->get()->toArray();
                
        		$finalNotificationData[$key]['first_name'] = $videoData[0]['users']['first_name'];
                $finalNotificationData[$key]['last_name'] = $videoData[0]['users']['last_name'];
                $finalNotificationData[$key]['user_name'] = $videoData[0]['users']['username'];
                $finalNotificationData[$key]['about_me'] = $videoData[0]['users']['about_me'];
                $finalNotificationData[$key]['created_at'] = \Carbon\Carbon::parse($videoData[0]['users']['created_at'])->format('Y-m-d h:m:s');
                $finalNotificationData[$key]['profile_picture'] = (isset($videoData[0]['users']['profile_picture'])) ? config('app.url').'/admin_theme/assets/profile_picture/'.$videoData[0]['users']['profile_picture'] : null;
                $finalNotificationData[$key]['user_id'] = $videoData[0]['users']['id'];
                $finalNotificationData[$key]['views'] = $videoData[0]['views'];
                $finalNotificationData[$key]['location'] = $videoData[0]['location'];
                $finalNotificationData[$key]['video_id'] = $videoData[0]['id'];
                $finalNotificationData[$key]['video_name'] = (isset($videoData[0]['video_name'])) ? $videoData[0]['video_name'] : null;
                $finalNotificationData[$key]['video_title'] = $videoData[0]['video_title'];
                    
                $finalNotificationData[$key]['likes'] = $videoData[0]['likes'];
                $finalNotificationData[$key]['like_by_me'] = $this->byMe($videoData[0]['id'],'like',$videoData[0]['user_id']);
                $finalNotificationData[$key]['shares'] = $videoData[0]['shares'];
                $finalNotificationData[$key]['saved_by_me'] = $this->byMe($videoData[0]['id'],'save',$videoData[0]['user_id']);

        	    $finalNotificationData[$key]['notification'] = $value['notificationdetails']['title'];
        	}

            $this->response['data'] = $finalNotificationData;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Get Notification Successfully";
        }else{
            $this->response['data'] = "";
            $this->response['status'] = $this->statusArr['not_found'];
            $this->response['message'] = "Notification not found";
        }

        return response()->json($this->response);
    }
}
