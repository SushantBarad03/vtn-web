<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\VideoActivity;
use App\Models\Report;
use App\Models\Followers;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function ValidateForm($fields, $rules){
        $validator = Validator::make($fields, $rules)->validate();
    }
    public function DTFilters($request){
        $filters = array(
            'draw' => $request['draw'],
            'offset' => $request['start'],
            'limit' => $request['length'],
            'sort_column' => $request['columns'][$request['order'][0]['column']]['data'],
            'sort_order' => $request['order'][0]['dir'],
            'search' => $request['search']['value']
        );
        return $filters;
    }

        /* Start Api Side Commons */
    public $response = array('message' => '', 'data'=> null);
    public $status = 412;
    public $paginate = 10;
    public $statusArr = [
        'success' => 200,
        'not_found' => 404,
        'unauthorised' => 412,
        'already_exist' => 409,
        'validation' => 422,
        'disabled' => 423,
        'something_wrong' => 405,
        'forbidden' => 403,
        'unauthenticated' => 401,
    ];

    public function ApiValidator($fields, $rules){
        $validator = Validator::make($fields, $rules);

        if($validator->fails())
        {
            $this->response['message'] = array_shift((array_values($validator->errors()->messages())[0]));
            return false;
        }
        return true;
    }

    //Null Converted to String
    public function convertNullToString($newUserData = null)
    {
        foreach ($newUserData as $key => $value) {
            $newData[$key] = !empty($value) ? $value : "";
        }
        return $newData;
    }

    //Convert Zero or One
    public function convertZeroOrOne()
    {
        
    }

    public function RADIANS($degrees)
    {
        return 0.0174532925 * $degrees;
    }

    //Get video activity
    public function getVideoActivityCount($video_id,$type)
    {
        return VideoActivity::where('video_id',$video_id)->where('type',$type)->count();
    }

    //Like by Me
    public function byMe($video_id,$type,$user_id)
    {
        $byMe = VideoActivity::where('video_id',$video_id)->where('type',$type)->where('user_id',$user_id)->count();
        return ($byMe == 1) ? 'true' : 'false';
    }

    //Follow by Me
    public function followByMe($user_id)
    {
        $followByMe = Followers::where('user_id',\Auth::user()->id)->where('followers',$user_id)->count();
        return ($followByMe == 1) ? 'true' : 'false';
    }

    /* Send Notifaction */
    public function notification($fcm_token, $message){
        //Send Notification
        $activeUsers = array (
            'to' => $fcm_token,
            'data' => array (
                "title" => 'Matimonial App',
                "body" => $message,
                "click_action" => "FCM_PLUGIN_ACTIVITY",
                "sound" => "default",
                "type" => 'notification'
            ),
        );
        $result = json_decode($this->sendPushNotification(json_encode($activeUsers)));        
        return $result;
    }

    public function sendPushNotification($activeUsers = ''){
        $result = '';
        if($activeUsers != ''){
            $activeUserData = json_decode($activeUsers);
            
            $notification = array('title' =>$activeUserData->data->title , 'text' => $activeUserData->data->body,'click_action' => 'OPEN_ACTIVITY_1');
            $data = array('title' =>$activeUserData->data->title , 'body' => $activeUserData->data->body,'content_available' => true,'priority' => 'high','type' => 'notification');

            $arrayToSend = array('to' => $activeUserData->to, 'data' => $data,'priority'=>'high','type' => 'notification');
            $json = json_encode($arrayToSend);

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key=AAAAJnQIutY:APA91bE9iNdT324qUvAPGxsYUjnY9hPe7NLGFz4k_3Gk1HqqgIWqU9OKLfxYc2VKK1OB56X-hk049-nS4mcSkBhfK6hf_bypXzSkPeP82iy6LePq-91rWF23-aoMP8Xqa8qNiPXLUhRt';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            //Send the request
            $result = curl_exec($ch);
            //Close request
            if ($result === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
        }
        return $result;
    }
    /* End Notification*/

    //Covert Digit to string
    public function convertFollowers($followers)
    {
        $input = number_format( intval( $followers ));
        $input_count = substr_count( $input, ',' );
        
        if ( $input_count != '0' ) {
            if ( $input_count == '1' ) {
                return substr( $input, 0, -4 ) . 'k';
            } elseif ( $input_count == '2' ) {
                return substr( $input, 0, -8 ) . 'm';
            } elseif ( $input_count == '3' ) {
                return substr( $input, 0, -12 ) . 'bn';
            } else {
                return;
            }
        } else {
            return $input;
        }        
    }

    //Covert Likes
    public function convertLikes($followers)
    {
        $input = number_format( intval( $followers ));
        $input_count = substr_count( $input, ',' );
        
        if ( $input_count != '0' ) {
            if ( $input_count == '1' ) {
                return substr( $input, 0, -4 ) . 'k';
            } elseif ( $input_count == '2' ) {
                return substr( $input, 0, -8 ) . 'm';
            } elseif ( $input_count == '3' ) {
                return substr( $input, 0, -12 ) . 'bn';
            } else {
                return;
            }
        } else {
            return $input;
        }        
    }

    //Check Profile Picture
    public function checkProfilePicture($profile_picture)
    {
        if(str_starts_with($profile_picture,'profile_picture')){
            return env('APP_URL')."/admin_theme/assets/profile_picture/".$profile_picture;
        }else{
            return $profile_picture;
        }
    }

    //User check video Reported or not
    public function videoReported($video_id)
    {
        return !empty(Report::where('video_id',$video_id)->where('user_id',\Auth::user()->id)->first()) ? 'true' : 'false';
    }
}