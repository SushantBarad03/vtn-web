<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Hashtag;
use App\Models\UserVideo;
use App\Models\Followers;
use App\Models\BackGroundMusic;
use App\Models\Notification;
use App\Models\NotificationDetails;
use App\Models\VideoComment;
use App\Models\City;
use App\Models\State;
use Validator; 
use Storage;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;


class VideoController extends Controller
{
    // Add Video Api
    public function addVideo(Request $request)
    {
    	$rules = [
    		'video_name' => 'required',
            'video_title' => 'required',
            'location' => 'required',
    	];

        $getDistrictId = City::where('city_name',$request->location)->value('district_id');
        $getStateId = City::where('city_name',$request->location)->value('state_id');
        
    	if($this->ApiValidator($request->all(), $rules)) {
	    	$videoData = new Video();
            $videoData->user_id = \Auth::user()->id;
	    	$videoData->video_title = \Str::limit($request->video_title, 150,);
            $videoData->district_id = $getDistrictId;
            $videoData->state_id = $getStateId;
	    	$videoData->location = $request->location;
            $videoData->views = '0';
            $videoData->likes = '0';
	    	$videoData->shares = '0';
	    	$videoData->save  =  '0';
	    	$videoData->report = '0';
	    	$videoData->status = '1';

	    	if ($request->hasFile('video_name')) {
                $file = $request->file('video_name');
                $file_new = str_replace(' ', '_', $file->getClientOriginalName());
                $path = $request->file('video_name')->storePublicly('public', 's3');
                // Storage::disk('s3')->setVisibility($file_new,'public');
                //$file->move(public_path().'/admin_theme/assets/videos/',$file_new);
                // $path = $file->storeAs('public',$file_new,'s3');
                $videoData->video_name = env('VIDEO_URL').$path;
            }
	    	$videoData->save();

            preg_match_all('/#(\w+)/', $videoData->video_title, $matches);
            foreach ($matches[1] as $key => $match) {
                $addHashtag = new Hashtag();
                $addHashtag->video_id = $videoData->id;
                $addHashtag->hashtag = $match;
                $addHashtag->save();
            }

            preg_match_all('/@(\w+)/', $videoData->video_title, $matches);
            foreach ($matches[1] as $key => $match) {
                $parentId = User::where('username',$match)->value('id');                
                $addAtTheRate = new UserVideo();
                $addAtTheRate->parent_user_id = $parentId;
                $addAtTheRate->user_id = $videoData->user_id;
                $addAtTheRate->video_id = $videoData->id;
                $addAtTheRate->type = 'tag';
                $addAtTheRate->status = '1';
                $addAtTheRate->save();
            }

            //Notification send
            $followers = Followers::with('users:id,fcm_token')->where('user_id',\Auth::user()->id)->get(['followers'])->toArray();

            // $followersNew = Followers::where('user_id',\Auth::user()->id)
            // ->with(['users'=> function($query){
            //                 $query->select(['users.id','users.fcm_token']);
            //               }])
            // ->get(['id','user_id','users.fcm_token'])->toArray();

            //Create notification details
            $notificationDetails = new NotificationDetails();
            $notificationDetails->title = $request->video_title;
            $notificationDetails->video_id = $videoData->id;
            $notificationDetails->status = "1";
            $notificationDetails->save();

            foreach ($followers as $key => $value) {

                $notification = new Notification();
                $notification->notification_id = $notificationDetails->id;
                $notification->user_id = $value['users'][0]['id'];;
                $notification->status = "1";
                $notification->save();

                $this->notification($value['users'][0]['fcm_token'],$notificationDetails->title);
            }

            $this->response['data'] = $videoData;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Add Video Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }

    // Delete Video
    public function deleteVideo(Request $request)
    {
        $rules = [
            'video_id' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
            
            $videoData = Video::where('id',$request->video_id)->first();
            if(!empty($videoData)){
                @unlink(public_path("/admin_theme/assets/videos/".$videoData->video_name));
                $videoData->delete();

                
                $this->response['data'] = $videoData;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Delete Video Successfully";
            }else{
                $this->response['data'] = "";
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "Video not found";
            }
        }else{
            $this->response['data'] = "";
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    //Get Tag user value
    public function getTagUserSuggestion(Request $request)
    {
        $rules = [
            'keyword' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
            $userData = User::where('username','like','%'.$request->keyword.'%')->get(['id','username'])->toArray();

            $this->response['data'] = $userData;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "User Suggestions retrieve Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    //Add video view
    public function addVideoView(Request $request)
    {
        $rules = [
            'video_id' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
            $videoData = Video::where('id',$request->video_id)->increment('views');
            if($videoData == true){
                $this->response['data'] = $videoData;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Add view on video Successfully";
            }else{
                $this->response['data'] = null;
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "Video not found";
            }
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    //Watch single Video
    public function video(Request $request)
    {
        $rules = [            
            'video_id' => 'required',
        ];        

        if($this->ApiValidator($request->all(), $rules)) {

            $videoData = Video::select('id','user_id','video_name','video_title','views','likes','shares','save','location')
                            ->where('status','1')
                            ->where('id',$request->video_id)
                            ->with('users:id,username,about_me,created_at,profile_picture')
                            ->get()->toArray();
            
            if(!empty($videoData)){
                $finalData = array();
                foreach ($videoData as $key => $value) {                    
                    $finalData[$key] = $value['users'];
                    $finalData[$key]['created_at'] = \Carbon\Carbon::parse($value['users']['created_at'])->format('Y-m-d h:m:s');
                    $finalData[$key]['profile_picture'] = (isset($value['users']['profile_picture'])) ? config('app.url').'/admin_theme/assets/profile_picture/'.$value['users']['profile_picture'] : null;
                    $finalData[$key]['views'] = $value['views'];
                    $finalData[$key]['location'] = $value['location'];
                    $finalData[$key]['video_id'] = $value['id'];
                    $finalData[$key]['video_name'] = (isset($value['video_name'])) ? $value['video_name'] : null;
                    $finalData[$key]['video_title'] = str_replace('#', '', $value['video_title']);
                    $finalData[$key]['likes'] = $this->getVideoActivityCount($value['id'],'like');
                    $finalData[$key]['like_by_me'] = $this->byMe($value['id'],'like',$value['user_id']);
                    $finalData[$key]['shares'] = $value['shares'];
                    $finalData[$key]['saved_by_me'] = $this->byMe($value['id'],'save',$value['user_id']);
                    $finalData[$key]['reported'] = $this->videoReported($value['id']);

                    $finalData[$key] = $this->convertNullToString($finalData[$key]);
                }
                
                $this->response['data'] = $finalData;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Video Successfully";
            }else{
                $this->response['data'] = null;
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "Video Not Found";
            }
            
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['not_found'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    //Get Suggestion
    public function getSuggestion(Request $request)
    {
        $rules = [
            'type' => 'required',
            'keyword' => 'required'
        ];        

        if($this->ApiValidator($request->all(), $rules)) {     
                $keyword = $request->keyword;

                if($request->type == '@'){
                    //Get People Data
                    $getPeople = User::where(function ($query) use ($keyword) {
                                  $query->where('username', 'like', '%' . $keyword . '%');
                            })->get()->toArray();


                    if(!empty($getPeople)){
                        foreach ($getPeople as $key => $value) {                    
                            $finalData['people'][$key]['id'] = $value['id'];
                            $finalData['people'][$key]['username'] = $value['username'];
                            $finalData['people'][$key]['profile_picture'] = $value['profile_picture'];
                            $finalData['people'][$key]['type'] = 'people';
                            $finalData['people'][$key] = $this->convertNullToString($finalData['people'][$key]);
                        }
                    }
                    $finalData = isset($finalData['people']) ? $finalData['people'] : array();
                }else if($request->type == '#'){
                    //Get HashTags Data
                    $getHashTag = Hashtag::select('hashtag')->where(function ($query1) use ($keyword) {
                                        $query1->where('hashtag', 'like', '%' . $keyword . '%');
                                    })->groupBy('hashtag')->get()->toArray();

                    if(!empty($getHashTag)){ 
                        foreach ($getHashTag as $key => $value) {
                            $finalData['hashtag'][$key]['id'] = Hashtag::where('hashtag',$value['hashtag'])->value('id');
                            $finalData['hashtag'][$key]['hashtag'] = $value['hashtag'];
                            $finalData['hashtag'][$key]['profile_picture'] = null;
                            $finalData['hashtag'][$key]['type'] = 'hashtag';
                            $finalData['hashtag'][$key] = $this->convertNullToString($finalData['hashtag'][$key]);
                        }
                    }
                    $finalData = isset($finalData['hashtag']) ? $finalData['hashtag'] : array();
                }
                
                if(!empty($finalData)){
                    $this->response['data'] = $finalData;
                    $this->response['status'] = $this->statusArr['success'];
                    $this->response['message'] = "Get Suggestions Successfully";
                }else{
                    $this->response['data'] =  array();
                    $this->response['status'] = $this->statusArr['success'];
                    $this->response['message'] = "Record not found";
                }
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['not_found'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    //Get BackGround Music
    public function getBackgroundMusic()
    {
        $backGroundMusicData = BackGroundMusic::where('status','1')->get(['id','music_name'])->toArray();
        
        if(!empty($backGroundMusicData)){
            $finalData = array();
            foreach ($backGroundMusicData as $key => $value) {
                $finalData[$key]['id'] = $value['id'];
                $finalData[$key]['music_name'] = (isset($value['music_name'])) ? config('app.url').'/public/admin_theme/assets/background_sound/'.$value['music_name'] : null;
                $finalData[$key] = $this->convertNullToString($finalData[$key]);
            }
            
            $this->response['data'] = $finalData;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Video Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['not_found'];
            $this->response['message'] = "BackGround Music Not Found";
        }
        return response()->json($this->response);
    }

    // Add Video Comment
    public function addVideoComment(Request $request)
    {      
        $rules = [
            'video_id' => 'required',
            'video_name' => 'required',
            'video_title' => 'required',
            'location' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {            
            $addVideo = new VideoComment();
            $addVideo->video_id = $request->video_id;
            $addVideo->user_id = \Auth::user()->id;
            $addVideo->video_title = \Str::limit($request->video_title, 150,);
            $addVideo->location = $request->location;
            $addVideo->views = '0';
            $addVideo->likes = '0';
            $addVideo->shares = '0';
            $addVideo->save  =  '0';
            $addVideo->report = '0';
            $addVideo->status = '1';

            if ($request->hasFile('video_name')) {
                $file = $request->file('video_name');
                $file_new = str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path().'/admin_theme/assets/video_comment/',$file_new);
                $addVideo->video_name = $file_new;
            }
            $addVideo->save();

            preg_match_all('/#(\w+)/', $addVideo->video_title, $matches);
            foreach ($matches[1] as $key => $match) {
                $addHashtag = new Hashtag();
                $addHashtag->video_id = $addVideo->id;
                $addHashtag->hashtag = $match;
                $addHashtag->save();
            }

            preg_match_all('/@(\w+)/', $addVideo->video_title, $matches);
            foreach ($matches[1] as $key => $match) {                
                $parentId = User::where('username',$match)->value('id');                
                $addAtTheRate = new UserVideo();
                $addAtTheRate->parent_user_id = $parentId;
                $addAtTheRate->user_id = $addVideo->user_id;
                $addAtTheRate->video_id = $addVideo->id;
                $addAtTheRate->type = 'tag';
                $addAtTheRate->status = '1';
                $addAtTheRate->save();
            }

            $this->response['data'] = $addVideo;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Add Video Comment Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }

    // Delete Video Comment
    public function deleteVideoComment(Request $request)
    {
        $rules = [
            'video_id' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
            $deleteVideoComment = VideoComment::where('id',$request->video_id)->first();
            if(!empty($deleteVideoComment)){
                @unlink(public_path("/admin_theme/assets/video_comment/".$deleteVideoComment->video_name));
                $deleteVideoComment->delete();

                $this->response['data'] = $deleteVideoComment;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Delete Video Successfully";
            }else{
                $this->response['data'] = "";
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "Video not found";
            }
        }else{
            $this->response['data'] = "";
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }
}