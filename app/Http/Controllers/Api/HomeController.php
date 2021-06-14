<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoActivity;
use App\Models\ReportComment;
use App\Models\UserVideo;
use App\Models\Comment;
use App\Models\Video;
use App\Models\User;
use App\Models\Report;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
    	$rules = [
            // 'lat' => 'required',
            // 'long' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {

                // $latitude = $userData[0]['latitude'];
                // $longitude = $userData[0]['longitude'];

                // $radius = 4000000;
            
                // $videoData = Video::select('id','user_id','video_name','video_title','views','likes','shares','save','location')
                //     ->selectRaw("id,latitude, longitude ,
                //         ( 6371000  acos( cos( radians(?) ) 
                //            cos( radians( latitude ) )
                //            * cos( radians( longitude ) - radians(?)
                //            ) + sin( radians(?) ) *
                //            sin( radians( latitude ) ) )
                //         ) AS distance", [$latitude, $longitude, $latitude])
                //     ->where('status','1')
                //     ->with('users:id,username,about_me,created_at,profile_picture')
                //     ->having("distance", "<", $radius)
                //     ->orderBy("distance",'asc')
                //     ->offset(0)
                //     ->limit(20)
                //     ->get()->toArray();
                // dd($videoData);
            
            // Only For City's Video
            $videoData = Video::select('id','user_id','video_name','video_title','views','likes','shares','save','state_id','district_id','location')
                ->where('status','1')
                // ->where('district_id',\Auth::user()->district)
                ->where('user_id','!=', \Auth::user()->id)
                ->where('district_id','=', \Auth::user()->district)
                ->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture,country,state,district,city')
                ->get()
                ->toArray();

            // All State Video
            $allVideoData = Video::select('id','user_id','video_name','video_title','views','likes','shares','save','state_id','district_id','location')
                ->where('status','1')
                // ->where('district_id',\Auth::user()->district)
                ->where('user_id','!=', \Auth::user()->id)
                ->where('district_id','!=', \Auth::user()->district)
                ->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture,country,state,district,city')
                ->get()
                ->toArray();

                $finalData = isset($videoData) ? $allVideoData : array();

                if(!empty($videoData) && !empty($allVideoData)){
                    $finalData = array_merge($videoData,$allVideoData);
                }

            if(!empty($finalData)){
                $finalData1 = array();
                foreach ($finalData as $key => $value) {
                    $finalData1[$key] = $value['users'];
                    $finalData1[$key]['user_name'] = (isset($value['users']['username'])) ? $value['users']['username'] : null;
                    unset($finalData1[$key]['username']);
                    $finalData1[$key]['user_id'] = isset($value['users']['id']) ? $value['users']['id']: null;
                    unset($finalData1[$key]['id']);
                    $finalData1[$key]['created_at'] = isset($value['users']['created_at']) ? \Carbon\Carbon::parse($value['users']['created_at'])->format('Y-m-d h:m:s') : null;
                    $finalData1[$key]['profile_picture'] = (isset($value['users']['profile_picture'])) ? $this->checkProfilePicture($value['users']['profile_picture']) : null;
                    $finalData1[$key]['views'] = $value['views'];
                    $finalData1[$key]['location'] = $value['location'];
                    $finalData1[$key]['video_id'] = $value['id'];
                    $finalData1[$key]['video_name'] = (isset($value['video_name'])) ? $value['video_name'] : null;
                    $finalData1[$key]['video_title'] = str_replace('#', '', $value['video_title']);
                    $finalData1[$key]['likes'] = $this->getVideoActivityCount($value['id'],'like');
                    $finalData1[$key]['like_by_me'] = $this->byMe($value['id'],'like',\Auth::user()->id);
                    $finalData1[$key]['shares'] = $value['shares'];
                    $finalData1[$key]['saved_by_me'] = $this->byMe($value['id'],'save',\Auth::user()->id);
                    $finalData1[$key]['reported'] = $this->videoReported($value['id']);

                    $finalData1[$key] = $this->convertNullToString($finalData1[$key]);
                }
                
                $this->response['data'] = $finalData1;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Get Videos Successfully";
            }else{
                $this->response['data'] = null;
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "Videos not available";
            }
            //dd($videoData[0]['users']['district']);
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }

    public function reportVideo(Request $request)
    {        
        $rules = [
            'video_id' => 'required',
            'reason' => 'required',
        ];
        
        if($this->ApiValidator($request->all(), $rules)) {
            $report = new Report();
            $report->user_id = \Auth::user()->id;
            $report->video_id = $request->video_id;
            $report->reason = $request->reason;
            $report->save();

            $this->response['data'] = $report;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Report add Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }
    
    public function videoActivity(Request $request)
    {        
        $rules = [
            'video_id' => 'required',
            'type' => 'required',
        ];
        if($this->ApiValidator($request->all(), $rules)) {
            if($request->type == 'like'){
                $videoActivity = new VideoActivity();
                $videoActivity->video_id = $request->video_id;
                $videoActivity->user_id = \Auth::user()->id;
                $videoActivity->type = $request->type;            
                $videoActivity->save(); 

                $this->response['message'] = "Like video Successfully";
            }else if($request->type == 'dislike'){
                VideoActivity::where('video_id',$request->video_id)->where('user_id',\Auth::user()->id)->where('type','like')->delete();

                $this->response['message'] = "Dislike video Successfully";
            }else if($request->type == 'save'){
                $videoActivity = new VideoActivity();
                $videoActivity->video_id = $request->video_id;
                $videoActivity->user_id = \Auth::user()->id;            
                $videoActivity->type = $request->type;            
                $videoActivity->save();                

                $this->response['message'] = "Save Video Successfully";
            }else if($request->type == 'unsave'){
                VideoActivity::where('video_id',$request->video_id)->where('user_id',\Auth::user()->id)->where('type','save')->delete();

                $this->response['message'] = "UnSave Video Successfully";
            }
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['success'];
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    public function videoComment(Request $request)
    {
        $rules = [
            'video_id' => 'required',
            'parent_comment_id' => 'required',
            'comment' => 'required',
        ];
         if($this->ApiValidator($request->all(), $rules)) {
            $comment = new Comment();
            $comment->video_id = $request->video_id;
            $comment->parent_comment_id = $request->parent_comment_id;
            $comment->user_id = \Auth::user()->id;            
            $comment->comment = $request->comment;   
            $comment->status = '1';
            $comment->save();                

            $this->response['data'] = $comment;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Add Comment Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    public function reportComment(Request $request)
    {
         $rules = [
            'video_id' => 'required',
            'comment_id' => 'required',
            'reason' => 'required',
        ];
         if($this->ApiValidator($request->all(), $rules)) {
            $reportComment = new ReportComment();
            $reportComment->user_id = \Auth::user()->id;
            $reportComment->video_id = $request->video_id;
            $reportComment->comment_id = $request->comment_id;
            $reportComment->reason = $request->reason;
            $reportComment->status = '1';
            $reportComment->save();                

            $this->response['data'] = $reportComment;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Add Comment Report Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }
}