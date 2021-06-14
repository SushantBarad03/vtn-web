<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Followers;
use App\Models\VideoActivity;
use App\Models\UserVideo;
use App\Models\HashtagFollow;
use App\Models\Hashtag;
use App\Models\ReportUser;
use App\Models\Report;
use App\Models\VideoComment;
use Validator;

class ProfileController extends Controller
{
    public function getUserProfile(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
            $userData = User::where('id',$request->user_id)->get(['id','first_name','last_name','username','profile_picture','about_me',])->toArray();
              
            if(!empty($userData)){
                // User Profile Picture
                $userData[0]['user_name'] = (isset($userData[0]['username'])) ? $userData[0]['username'] : null;
                $userData[0]['user_id'] = (isset($userData[0]['id'])) ? $userData[0]['id'] : null;
                $userData[0]['profile_picture'] = (isset($userData[0]['profile_picture'])) ? $this->checkProfilePicture($userData[0]['profile_picture']) : null;

                // User Saved Video
                $savedVideoData = UserVideo::with(['video:id,user_id,video_name,video_title,views,shares,likes,save,location,created_at'])->where('status','1')->where('user_id',$request->user_id)->where('type','saved')->get()->toArray();

                $userData[0]['saved_video'] = array();
                foreach ($savedVideoData as $key => $value) {
                    $singleUserData = User::where('id',$value['video']['user_id'])->get(['id','first_name','last_name','username','about_me'])->toArray();

                    $userData[0]['saved_video'][$key]['first_name'] = $singleUserData[0]['first_name'];
                    $userData[0]['saved_video'][$key]['last_name'] = $singleUserData[0]['last_name'];
                    $userData[0]['saved_video'][$key]['about_me'] = $singleUserData[0]['about_me'];
                    $userData[0]['saved_video'][$key]['created_at'] = isset($value['video']['created_at']) ? \Carbon\Carbon::parse($value['video']['created_at'])->format('Y-m-d h:m:s') : null;
                    $userData[0]['saved_video'][$key]['profile_picture'] = (isset($singleUserData[0]['profile_picture'])) ? $this->checkProfilePicture($singleUserData[0]['profile_picture']) : null;
                    $userData[0]['saved_video'][$key]['user_name'] = $singleUserData[0]['username'];
                    $userData[0]['saved_video'][$key]['user_id'] = $singleUserData[0]['id'];
                    $userData[0]['saved_video'][$key]['views'] = $value['video']['views'];
                    $userData[0]['saved_video'][$key]['location'] = $value['video']['location'];
                    $userData[0]['saved_video'][$key]['video_id'] = $value['video']['id'];
                    $userData[0]['saved_video'][$key]['video_name'] = (isset($value['video']['video_name'])) ? $value['video']['video_name'] : null;
                    $userData[0]['saved_video'][$key]['video_title'] = $value['video']['video_title'];
                    $userData[0]['saved_video'][$key]['likes'] = $this->getVideoActivityCount($value['id'],'like');
                    $userData[0]['saved_video'][$key]['like_by_me'] = $this->byMe($value['id'],'like',\Auth::user()->id);                        
                    $userData[0]['saved_video'][$key]['shares'] = $value['video']['shares'];                        
                    $userData[0]['saved_video'][$key]['saved_by_me'] = $this->byMe($value['id'],'save',\Auth::user()->id);
                    $userData[0]['saved_video'][$key]['reported'] = $this->videoReported($value['video']['id']);
                }      
                    
                // User Tag Video
                $tagVideoData = UserVideo::with(['video:id,user_id,video_name,video_title,views,shares,likes,save,location,created_at'])->where('status','1')->where('user_id',$request->user_id)->where('type','tag')->get()->toArray();
                    
                $userData[0]['tag_video'] = array();
                foreach ($tagVideoData as $key2 => $value2) {
                    $singleTagUserData = User::where('id',$value2['video']['user_id'])->get(['id','first_name','last_name','username','profile_picture','about_me'])->toArray();

                    $userData[0]['tag_video'][$key2]['first_name'] = $singleTagUserData[0]['first_name'];
                    $userData[0]['tag_video'][$key2]['last_name'] = $singleTagUserData[0]['last_name'];
                    $userData[0]['tag_video'][$key2]['about_me'] = $singleTagUserData[0]['about_me'];
                    $userData[0]['tag_video'][$key2]['created_at'] = isset($value2['video']['created_at']) ? \Carbon\Carbon::parse($value2['video']['created_at'])->format('Y-m-d h:m:s') : null;
                    $userData[0]['tag_video'][$key2]['profile_picture'] = (isset($singleUserData[0]['profile_picture'])) ? $this->checkProfilePicture($singleUserData[0]['profile_picture']) : null;
                    $userData[0]['tag_video'][$key2]['user_name'] = $singleTagUserData[0]['username'];
                    $userData[0]['tag_video'][$key2]['user_id'] = $singleTagUserData[0]['id'];
                    $userData[0]['tag_video'][$key2]['views'] = $value2['video']['views'];
                    $userData[0]['tag_video'][$key2]['location'] = $value2['video']['location'];
                    $userData[0]['tag_video'][$key2]['video_id'] = $value2['video']['id'];
                    $userData[0]['tag_video'][$key2]['video_name'] = (isset($value2['video']['video_name'])) ? $value2['video']['video_name'] : null;
                    $userData[0]['tag_video'][$key2]['video_title'] = $value2['video']['video_title'];
                    $userData[0]['tag_video'][$key2]['likes'] = $this->getVideoActivityCount($value2['id'],'like');
                    $userData[0]['tag_video'][$key2]['like_by_me'] = $this->byMe($value2['id'],'like',\Auth::user()->id);                        
                    $userData[0]['tag_video'][$key2]['shares'] = $value2['video']['shares'];                        
                    $userData[0]['tag_video'][$key2]['saved_by_me'] = $this->byMe($value2['id'],'save',\Auth::user()->id);
                    $userData[0]['tag_video'][$key2]['reported'] = $this->videoReported($value2['video']['id']);
                }

                $userData[0]['followers'] = Followers::where('user_id',$request->user_id)->count();
                $userData[0]['following'] = Followers::where('followers',$request->user_id)->count();
                $userData[0]['likes'] = $this->convertLikes(Video::where('user_id',$request->user_id)->sum('likes'));
                $report = ReportUser::where('user_id',\Auth::user()->id)->where('target_user_id',$userData[0]['id'])->where('status','1')->first();
                $userData[0]['report'] = !empty($report) ? 'true' : 'false';
                
                $followersCount = $this->convertFollowers($userData[0]['followers']);

                //Get Videos
                $videoData = Video::select('id','user_id','video_name','video_title','views','likes','shares','save','location','created_at')
                            ->where('status','1')
                            ->where('user_id',$request->user_id)
                            ->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture')
                            ->get()->toArray();

                $userData[0]['video'] = array();
                if(!empty($videoData)){
                    foreach ($videoData as $key1 => $value1) { 
                        $userData[0]['video'][$key1]['first_name'] = $value1['users']['first_name'];
                        $userData[0]['video'][$key1]['last_name'] = $value1['users']['last_name'];
                        $userData[0]['video'][$key1]['about_me'] = $value1['users']['about_me'];
                        $userData[0]['video'][$key1]['created_at'] = isset($value1['created_at']) ? \Carbon\Carbon::parse($value1['created_at'])->format('Y-m-d h:m:s') : null;
                        $userData[0]['video'][$key1]['profile_picture'] = (isset($value1['profile_picture'])) ? $this->checkProfilePicture($value1['profile_picture']) : null;
                        $userData[0]['video'][$key1]['user_name'] = $value1['users']['username'];
                        $userData[0]['video'][$key1]['user_id'] = $value1['users']['id'];
                        $userData[0]['video'][$key1]['views'] = $value1['views'];
                        $userData[0]['video'][$key1]['location'] = $value1['location'];
                        $userData[0]['video'][$key1]['video_id'] = $value1['id'];
                        $userData[0]['video'][$key1]['video_name'] = (isset($value1['video_name'])) ? $value1['video_name'] : null;
                        $userData[0]['video'][$key1]['video_title'] = $value1['video_title'];
                        $userData[0]['video'][$key1]['likes'] = $this->getVideoActivityCount($value1['id'],'like');
                        $userData[0]['video'][$key1]['like_by_me'] = $this->byMe($value1['id'],'like',\Auth::user()->id);
                        $userData[0]['video'][$key1]['shares'] = $value1['shares'];
                        $userData[0]['video'][$key1]['saved_by_me'] = $this->byMe($value1['id'],'save',\Auth::user()->id);
                        $userData[0]['video'][$key1]['reported'] = $this->videoReported($value1['id']);
                    }
                }     
                $userData[0] = $this->convertNullToString($userData[0]);
                
                $array = $userData[0];
                unset($array['username']);     

                if($array['saved_video'] == ""){
                    $array['saved_video'] = array();
                }

                if($array['tag_video'] == ""){
                    $array['tag_video'] = array();
                }

                if($array['video'] == ""){
                    $array['video'] = array();
                }

                $this->response['data'] = $array;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Get Profile Successfully";
            }else{
                $this->response['data'] = null;
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "User Not Found";
            }
        
            $this->response['data'] = $array;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Get Profile Successfully";

        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = $this->response['message'];
        }
        return response()->json($this->response);
    }

    public function editUserProfile(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'about_me' => 'required',
        ];
        if($this->ApiValidator($request->all(), $rules)) {
            //Update User Info
            if($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                @unlink(public_path("/admin_theme/assets/profile_picture/".$oldFilename));

                $file_new = 'profile_picture_'.\Auth::user()->id .'.' . str_replace(' ', '_', $file->getClientOriginalExtension());
                $file->move(public_path().'/admin_theme/assets/profile_picture/',$file_new);
                
                $profilePicture = $file_new;
            }

            $user = User::where('id', \Auth::user()->id)->first();      
            if (!empty($user)) {   
                User::where('id', \Auth::user()->id)->update([
                    'first_name' => $request->first_name, 
                    'last_name' => $request->last_name,
                    'username' => $request->username,
                    'about_me' => $request->about_me,
                    'profile_picture' => isset($profilePicture) ? $profilePicture : $user->profile_picture,
                ]); 
                $data = $user->refresh()->toArray();
                $data['profile_picture'] = $this->checkProfilePicture($data['profile_picture']);

                $this->response['data'] =  $this->convertNullToString($data);
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Update Successfully";
            }   
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }


    public function getProfile(Request $request)
    {
        $userData = User::where('id',\Auth::user()->id)->get(['id','first_name','last_name','username','profile_picture','about_me',])->toArray();

        $userData[0]['profile_picture'] = (isset($userData[0]['profile_picture'])) ? $this->checkProfilePicture($userData[0]['profile_picture']) : null;

        // Get User Videos
        // $videoData = Video::where('user_id',\Auth::user()->id)->get(['id','video_name','video_title','views','shares','likes','save','location','created_at'])->toArray();

        $videoData = Video::select('id','user_id','video_name','video_title','views','likes','shares','save','location','created_at')
                            ->where('status','1')
                            ->where('user_id',\Auth::user()->id)
                            ->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture')
                            ->get()->toArray();
        
        $userData[0]['video'] = array();
        foreach ($videoData as $key => $value) {
            // $userData[0]['video'][$key] = $value;            
            $userData[0]['video'][$key]['first_name'] = $value['users']['first_name'];
            $userData[0]['video'][$key]['last_name'] = $value['users']['last_name'];
            $userData[0]['video'][$key]['about_me'] = $value['users']['about_me'];
            $userData[0]['video'][$key]['created_at'] = isset($value['created_at']) ? \Carbon\Carbon::parse($value['created_at'])->format('Y-m-d h:m:s') : null;
            $userData[0]['video'][$key]['profile_picture'] = (isset($value['profile_picture'])) ? $this->checkProfilePicture($value['profile_picture']) : null;
            $userData[0]['video'][$key]['user_name'] = $value['users']['username'];
            $userData[0]['video'][$key]['user_id'] = $value['users']['id'];
            $userData[0]['video'][$key]['views'] = $value['views'];
            $userData[0]['video'][$key]['location'] = $value['location'];
            $userData[0]['video'][$key]['video_id'] = $value['id'];
            $userData[0]['video'][$key]['video_name'] = (isset($value['video_name'])) ? $value['video_name'] : null;
            $userData[0]['video'][$key]['video_title'] = $value['video_title'];
            $userData[0]['video'][$key]['likes'] = $this->getVideoActivityCount($value['id'],'like');
            $userData[0]['video'][$key]['like_by_me'] = $this->byMe($value['id'],'like',\Auth::user()->id);                        
            $userData[0]['video'][$key]['shares'] = $value['shares'];
            $userData[0]['video'][$key]['saved_by_me'] = $this->byMe($value['id'],'save',\Auth::user()->id);
            $userData[0]['video'][$key]['reported'] = $this->videoReported($value['id']);            
        }  

        if(!empty($userData)){
            $userData[0]['user_name'] = (isset($userData[0]['username'])) ? $userData[0]['username'] : null;
            // Unset username
            unset($userData[0]['username']);
            
            // User Profiel Picture
            $userData[0]['profile_picture'] = (isset($userData[0]['profile_picture'])) ? $this->checkProfilePicture($userData[0]['profile_picture']) : null;

            // User Saved Video
            $savedVideoData = VideoActivity::with(['video:id,user_id,video_name,video_title,views,shares,likes,save,location,created_at'])->where('user_id',\Auth::user()->id)->where('type','save')->get()->toArray();            
            
            $userData[0]['saved_video'] = array();
            foreach ($savedVideoData as $key => $value) {
                $singleUserData = User::where('id',$value['video']['user_id'])->get(['id','first_name','last_name','username','about_me'])->toArray();

                $userData[0]['saved_video'][$key]['first_name'] = $singleUserData[0]['first_name'];
                $userData[0]['saved_video'][$key]['last_name'] = $singleUserData[0]['last_name'];
                $userData[0]['saved_video'][$key]['about_me'] = $singleUserData[0]['about_me'];
                $userData[0]['saved_video'][$key]['created_at'] = isset($value['video']['created_at']) ? \Carbon\Carbon::parse($value['video']['created_at'])->format('Y-m-d h:m:s') : null;
                $userData[0]['saved_video'][$key]['profile_picture'] = (isset($singleUserData[0]['profile_picture'])) ? $this->checkProfilePicture($singleUserData[0]['profile_picture']) : null;
                $userData[0]['saved_video'][$key]['user_name'] = $singleUserData[0]['username'];
                $userData[0]['saved_video'][$key]['user_id'] = $singleUserData[0]['id'];
                $userData[0]['saved_video'][$key]['views'] = $value['video']['views'];
                $userData[0]['saved_video'][$key]['location'] = $value['video']['location'];
                $userData[0]['saved_video'][$key]['video_id'] = $value['video']['id'];
                $userData[0]['saved_video'][$key]['video_name'] = (isset($value['video']['video_name'])) ? $value['video']['video_name'] : null;
                $userData[0]['saved_video'][$key]['video_title'] = $value['video']['video_title'];
                $userData[0]['saved_video'][$key]['likes'] = $this->getVideoActivityCount($value['id'],'like');
                $userData[0]['saved_video'][$key]['like_by_me'] = $this->byMe($value['id'],'like',\Auth::user()->id);                        
                $userData[0]['saved_video'][$key]['shares'] = $value['video']['shares'];                        
                $userData[0]['saved_video'][$key]['saved_by_me'] = $this->byMe($value['id'],'save',\Auth::user()->id);
                $userData[0]['saved_video'][$key]['reported'] = $this->videoReported($value['video']['id']);
            }
                
            // User Tag Video
            $tagVideoData = UserVideo::with(['video:id,user_id,video_name,video_title,views,shares,likes,save,location,created_at'])->where('user_id',\Auth::user()->id)->where('type','tag')->get()->toArray();
            
            $userData[0]['tag_video'] = array();
            foreach ($tagVideoData as $key2 => $value2) {
                $singleTagUserData = User::where('id',$value2['video']['user_id'])->get(['id','first_name','last_name','username','profile_picture','about_me'])->toArray();

                // $userData[0]['tag_video'][$key2] = $value2['video'];

                $userData[0]['tag_video'][$key2]['first_name'] = $singleTagUserData[0]['first_name'];
                $userData[0]['tag_video'][$key2]['last_name'] = $singleTagUserData[0]['last_name'];
                $userData[0]['tag_video'][$key2]['about_me'] = $singleTagUserData[0]['about_me'];
                $userData[0]['tag_video'][$key2]['created_at'] = isset($value2['video']['created_at']) ? \Carbon\Carbon::parse($value2['video']['created_at'])->format('Y-m-d h:m:s') : null;
                $userData[0]['tag_video'][$key2]['profile_picture'] = (isset($singleUserData[0]['profile_picture'])) ? $this->checkProfilePicture($singleUserData[0]['profile_picture']) : null;
                $userData[0]['tag_video'][$key2]['user_name'] = $singleTagUserData[0]['username'];
                $userData[0]['tag_video'][$key2]['user_id'] = $singleTagUserData[0]['id'];
                $userData[0]['tag_video'][$key2]['views'] = $value2['video']['views'];
                $userData[0]['tag_video'][$key2]['location'] = $value2['video']['location'];
                $userData[0]['tag_video'][$key2]['video_id'] = $value2['video']['id'];
                $userData[0]['tag_video'][$key2]['video_name'] = (isset($value2['video']['video_name'])) ? $value2['video']['video_name'] : null;
                $userData[0]['tag_video'][$key2]['video_title'] = $value2['video']['video_title'];
                $userData[0]['tag_video'][$key2]['likes'] = $this->getVideoActivityCount($value2['id'],'like');
                $userData[0]['tag_video'][$key2]['like_by_me'] = $this->byMe($value2['id'],'like',\Auth::user()->id);                        
                $userData[0]['tag_video'][$key2]['shares'] = $value2['video']['shares'];                        
                $userData[0]['tag_video'][$key2]['saved_by_me'] = $this->byMe($value2['id'],'save',\Auth::user()->id);
                $userData[0]['tag_video'][$key2]['reported'] = $this->videoReported($value2['video']['id']);
            }            

            // Video Comment 
            $videoComment = VideoComment::select('id','user_id','video_name','video_title','views','location','created_at')
                ->where('status','1')
                ->where('user_id',\Auth::user()->id)
                ->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture')
                ->get()
                ->toArray();

            $userData[0]['video_comment'] = array();
            foreach ($videoComment as $key2 => $value2) {
                $userData[0]['video_comment'][$key2]['first_name'] = $value2['users'][0]['first_name'];
                $userData[0]['video_comment'][$key2]['last_name'] = $value2['users'][0]['last_name'];
                $userData[0]['video_comment'][$key2]['user_name'] = $value2['users'][0]['username'];
                $userData[0]['video_comment'][$key2]['created_at'] = isset($value2['created_at']) ? \Carbon\Carbon::parse($value2['created_at'])->format('Y-m-d h:m:s') : null;
                $userData[0]['video_comment'][$key2]['video_id'] = $value2['id'];
                $userData[0]['video_comment'][$key2]['video_name'] = (isset($value2['video_name'])) ? $value2['video_name'] : null;
                $userData[0]['video_comment'][$key2]['video_title'] = $value2['video_title']; 
                $userData[0]['video_comment'][$key2]['views'] = $value2['views'];               
                $userData[0]['video_comment'][$key2]['location'] = $value2['location'];
                $userData[0]['video_comment'][$key2]['like_by_me'] = $this->byMe($value2['id'],'like',\Auth::user()->id);
            }
            
            $userData[0]['followers'] = Followers::where('followers',\Auth::user()->id)->count();
            $userData[0]['following'] = Followers::where('user_id',\Auth::user()->id)->count();
            $followersCount = $this->convertFollowers($userData[0]['followers']);
            $userData[0] = $this->convertNullToString($userData[0]);

            if($userData[0]['saved_video'] == ""){
                $userData[0]['saved_video'] = array();
            }

            if($userData[0]['tag_video'] == ""){
                $userData[0]['tag_video'] = array();
            }

            if($userData[0]['video'] == ""){
                $userData[0]['video'] = array();
            }
            if($userData[0]['video_comment'] == ""){
                $userData[0]['video_comment'] = array();
            }

            // if(!empty($userData[0]['video'])){
            //     foreach ($userData[0]['video'] as $key1 => $value1) {                       
            //         $userData[0]['video'][$key1]['video_name'] = (isset($value1['video_name'])) ? $value1['video_name'] : null;                        
            //         $userData[0]['likes'] = $this->getVideoActivityCount($value1['id'],'like');
            //         $userData[0]['like_by_me'] = $this->byMe($value1['id'],'like',\Auth::user()->id);
            //         $userData[0]['shares'] = $value1['shares'];
            //         $userData[0]['saved_by_me'] = $this->byMe($value1['id'],'save',\Auth::user()->id);
            //     }
            // }

            $this->response['data'] = (!empty($userData[0])) ? $userData[0] : null;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Get User Profile Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['not_found'];
            $this->response['message'] = "User Not Found";
        }
        return response()->json($this->response);
    }
    
    public function follow(Request $request)
    {
        $rules = [
            'type' => 'required',
        ];
      
        if($this->ApiValidator($request->all(), $rules)) {
            if($request->type == 'follow'){
                if(!empty($request->followers_id)){
                    $followers = new Followers();
                    $followers->followers = $request->followers_id;
                    $followers->user_id = \Auth::user()->id;
                    $followers->save();
                }
                if(!empty($request->hashtag)){
                    $followers = new HashtagFollow();
                    $followers->user_id = \Auth::user()->id;
                    $followers->hashtag = $request->hashtag;
                    $followers->save();
                }

                $this->response['data'] = $followers;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Follow User Successfully";
            }else{
                $unFollow = Followers::where('followers', $request->followers_id)->delete();
                $unFollow = HashtagFollow::where('hashtag', $request->hashtag)->delete();

                $this->response['data'] = $unFollow;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "unFollow User Successfully";
            }
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }

    public function myFollowers(Request $request)
    {
        $userData = Followers::with('userfollowing:id,first_name,last_name,username,profile_picture')->where('followers',\Auth::user()->id)->select('user_id')->get()->toArray();
        $finalData = array();
        foreach ($userData as $key => $value) {
            $finalData[$key] = $value['userfollowing'][0];
            $finalData[$key]['user_name'] = (isset($value['userfollowing'][0]['username'])) ? $value['userfollowing'][0]['username'] : null;
            $finalData[$key]['user_id'] = $value['user_id'];
            $finalData[$key]['profile_picture'] = (isset($value['userfollowing'][0]['profile_picture'])) ? $this->checkProfilePicture($value['userfollowing'][0]['profile_picture']) : null;
            $finalData[$key]['followByMe'] = $this->followByMe($value['userfollowing'][0]['id']);

            $finalData[$key] = $this->convertNullToString($finalData[$key]);
        }
            
        $array = $finalData;
        unset($array['username']);   
        $this->response['data'] = $array;
        $this->response['status'] = $this->statusArr['success'];
        $this->response['message'] = "Followers";

        return response()->json($this->response);
    }

    public function myFollowings(Request $request)
    {
        //Get HashTag
        $followHastag = HashtagFollow::where('user_id',\Auth::user()->id)->get('hashtag')->toArray();

        $hashTagfinalData = array();
        foreach ($followHastag as $key => $value) {                
            $hashTagfinalData[$key]['hashtag'] = $value['hashtag'];
            $hashTagfinalData[$key]['videos_count'] = Hashtag::where('hashtag',$value['hashtag'])->count();
            $hashTagfinalData[$key]['profile_picture'] = null;
            $hashTagfinalData[$key] = $this->convertNullToString($hashTagfinalData[$key]);
        }

        //Get People Follows
        $userData = Followers::select('followers','user_id')->where('user_id',\Auth::user()->id)->with('users:id,first_name,last_name,username,profile_picture')->get()->toArray();
        
        $finalData = array();
        foreach ($userData as $key => $value) {
            $finalData[$key]['user_id'] = $value['followers'];
            $finalData[$key]['first_name'] = $value['users'][0]['first_name'];
            $finalData[$key]['last_name'] = $value['users'][0]['last_name'];
            $finalData[$key]['user_name'] = (isset($value['users'][0]['username'])) ? $value['users'][0]['username'] : null;
            $finalData[$key]['profile_picture'] = (isset($value['users'][$key]['profile_picture'])) ? $this->checkProfilePicture($value['users'][$key]['profile_picture']) : null;

            $finalData[$key] = $this->convertNullToString($finalData[$key]);
        }

        $data['people'] = $finalData;
        $data['hashtag'] = $hashTagfinalData;

        $this->response['data'] = $data;
        $this->response['status'] = $this->statusArr['success'];
        $this->response['message'] = "Following";

        return response()->json($this->response);
    }

    public function changeSettings(Request $request)
    {
        $rules = [
            'action_type' => 'required',
            'status' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
            if($request->action_type == 'notification'){ 
                $settings = User::where('id', \Auth::user()->id)->update(['notification' => $request->status]);                
            }else if($request->action_type == 'auto_play'){
                $settings = User::where('id', \Auth::user()->id)->update(['auto_play' => $request->status]);
            }        

            $this->response['data'] = $settings;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Update Record Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }

    public function saveVideo(Request $request)
    {
        $rules = [
            'parent_user_id' => 'required',
            'user_id' => 'required',
            'video_id' => 'required',            
        ];

        if($this->ApiValidator($request->all(), $rules)) {
            $userUserVideo = new UserVideo();
            $userUserVideo->parent_user_id = $request->parent_user_id;
            $userUserVideo->user_id = \Auth::user()->id;
            $userUserVideo->video_id = $request->video_id;
            $userUserVideo->type = 'saved';
            $userUserVideo->status = '1';
            $userUserVideo->save();                

            $this->response['data'] = $userUserVideo;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Add Record Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }

    public function storeLocation(Request $request)
    {
        $user = User::where('id', \Auth::user()->id)->first();
        if (!empty($user)) {
            User::where('id', \Auth::user()->id)->update([
                'country' => $request->country_id,
                'state' => $request->state_id,
                'district' => $request->district_id,
                'city' => $request->city_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
            $data = $user->refresh()->toArray();
            
            $this->response['data'] = $this->convertNullToString($data);
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Add Record Successfully";
        }

        return response()->json($this->response);
    }

    // //Get video activity
    // public function getVideoActivityCount($video_id,$type)
    // {
    //     return VideoActivity::where('video_id',$video_id)->where('type',$type)->count();
    // }

    //Like by Me
    // public function byMe($video_id,$type,$user_id)
    // {
    //     $byMe = VideoActivity::where('video_id',$video_id)->where('type',$type)->where('user_id',$user_id)->count();
    //     return ($byMe == 1) ? 'true' : 'false';
    // }

    //Report User
    public function reportUser(Request $request)
    {        
        $rules = [
            'user_id' => 'required',
            'target_user_id' => 'required',
            'reason' => 'required',
        ];
        
        if($this->ApiValidator($request->all(), $rules)) {
            $report = new ReportUser();
            $report->user_id = $request->user_id;
            $report->target_user_id = $request->target_user_id;
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

    //Change Location
    public function changeLocation(Request $request)
    {
        dd($request->all());
    }
}