<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hashtag;
use App\Models\UserVideo;
use App\Models\Video;
use App\Models\User;
use App\Models\VideoActivity;
use App\Models\Followers;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function searchData(Request $request)
    {
        //Get Hash data
        $hashData = Hashtag::select('hashtag')->distinct('hashtag')->get()->toArray();

        //Get popular users
        $userData = Followers::select('followers','user_id')->where('user_id','!=', \Auth::user()->id)->with('users:id,first_name,last_name,username,profile_picture')->get()->toArray();
        
        //$popularUserData = Followers::select('user_id')->distinct('user_id')->get()->toArray();
        $popularUserData = Followers::where('followers','!=',\Auth::user()->id)->get(['user_id'])->toArray();
        // sort($popularUserData);
        // print_r($popularUserData);die();
        
        $popularUser = array();
        foreach ($popularUserData as $key => $value) {
            $userData = User::where('id',$value['user_id'])->get(['id','first_name','last_name','profile_picture','district'])->toArray();

            $popularUser[$key]['id'] = $userData[0]['id'];
            $popularUser[$key]['user_name'] = $userData[0]['first_name'] .' '. $userData[0]['last_name'];
            $popularUser[$key]['profile_picture'] = (isset($userData[0]['profile_picture'])) ? $this->checkProfilePicture($userData[0]['profile_picture']) : '';
            $popularUser[$key]['first_name'] = $userData[0]['first_name'];
            $popularUser[$key]['last_name'] =  $userData[0]['last_name'];
            $popularUser[$key]['district'] =  $userData[0]['district'];
        }

        // $trendingVideo = Video::with('users:id,first_name,last_name,username,about_me,created_at,profile_picture')->select('id','user_id','video_name','video_title','views','likes','shares','save','location','created_at')->get()->toArray();

         // Only For City's Trending Video
        $trendingVideo = Video::where('district_id','=', \Auth::user()->district)->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture')->select('id','user_id','video_name','video_title','views','likes','shares','save','location','created_at','district_id')->get()->toArray();

        // State Trending Video
        $allTrendingVideo = Video::where('district_id','!=', \Auth::user()->district)->with('users:id,first_name,last_name,username,about_me,created_at,profile_picture')->select('id','user_id','video_name','video_title','views','likes','shares','save','location','created_at','district_id')->get()->toArray();

        $finalData1 = isset($trendingVideo) ? $allTrendingVideo : array();

        if(!empty($trendingVideo) && !empty($allTrendingVideo)){
            $finalData1 = array_merge($trendingVideo,$allTrendingVideo);
        }
        
        if(!empty($finalData1)){
            foreach ($finalData1 as $key => $value) {
                $finalData[$key]['first_name'] = (isset($value['users']['first_name'])) ? $value['users']['first_name'] : null;
                $finalData[$key]['last_name'] = (isset($value['users']['last_name'])) ? $value['users']['last_name'] : null;
                $finalData[$key]['about_me'] = (isset($value['users']['about_me'])) ? $value['users']['about_me'] : null;
                $finalData[$key]['created_at'] = isset($value['users']['created_at']) ? \Carbon\Carbon::parse($value['users']['created_at'])->format('Y-m-d h:m:s') : null;
                $finalData[$key]['profile_picture'] = (isset($value['users']['profile_picture'])) ? $this->checkProfilePicture($value['users']['profile_picture']) : '';
                $finalData[$key]['user_name'] = (isset($value['users']['username'])) ? $value['users']['username'] : null;
                $finalData[$key]['user_id'] = $value['user_id'];
                $finalData[$key]['views'] = $value['views'];
                $finalData[$key]['location'] = $value['location'];
                $finalData[$key]['video_id'] = $value['id'];
                $finalData[$key]['video_name'] = (isset($value['video_name'])) ? $value['video_name'] : null;
                $finalData[$key]['video_title'] = $value['video_title'];
                $finalData[$key]['likes'] = $this->getVideoActivityCount($value['id'],'like');
                $finalData[$key]['like_by_me'] = $this->byMe($value['id'],'like',\Auth::user()->id);
                $finalData[$key]['shares'] = $value['shares'];
                $finalData[$key]['saved_by_me'] = $this->byMe($value['id'],'save',\Auth::user()->id);
                $finalData[$key]['reported'] = $this->videoReported($value['id']);
            }
        }
        
        // if(!empty($popularUser)){
        //     foreach ($popularUser as $key => $value) {
        //         $profilePicture['id'] = $value['user']['id'];
        //         $profilePicture['username'] = $value['user']['username'];
        //         $profilePicture['profile_picture'] = url('/admin_theme/assets/profile_picture/').'/' .$value['user']['profile_picture'];
        //         $popularUser[$key]['user'] = $profilePicture;
        //     }
        // }

        $data = array();
        $data['hashtag'] = $hashData;
        $data['popular_user'] = $popularUser;
        $data['trending_video'] = $finalData;
            
        $array = $data;
        unset($array['username']);

        if (!empty($data)) {
            $this->response['data'] = $data;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Get Hash Tag Successfully";
        }else{
            $this->response['data'] = $data;
            $this->response['status'] = $this->statusArr['not_found'];
            $this->response['message'] = "Not Found";
        }

        return response()->json($this->response);
    }

    public function getHashTagVideos(Request $request)
    {
        $rules = [
            'hashtag' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
     
            $hashTagVideo = Hashtag::with(['video:id,user_id,video_name,video_title,views,shares,likes,save,location,created_at'])->where('hashtag',$request->hashtag)->get(['id','video_id','hashtag',])->toArray();

            $finalData['hashtag_name'] = $request->hashtag;
            $finalData['hashtag_count'] = !empty($hashTagVideo[0]['video']) ? count($hashTagVideo[0]['video']) : '0';
            $finalData['hashTagData'] = array();

            if(!empty($hashTagVideo[0]['video'])){
                foreach ($hashTagVideo as $key => $value) {                                 
                    $singleUserData = User::where('id',$value['video']['user_id'])->get(['id','first_name','last_name','username','about_me'])->toArray();


                    $hashTagVideoData[$key]['first_name'] = $singleUserData[0]['first_name'];
                    $hashTagVideoData[$key]['last_name'] = $singleUserData[0]['last_name'];
                    $hashTagVideoData[$key]['about_me'] = $singleUserData[0]['about_me'];
                    $hashTagVideoData[$key]['created_at'] = isset($value['video']['created_at']) ? \Carbon\Carbon::parse($value['video']['created_at'])->format('Y-m-d h:m:s') : null;
                    $hashTagVideoData[$key]['profile_picture'] = (isset($singleUserData[0]['profile_picture'])) ? $this->checkProfilePicture($singleUserData[0]['profile_picture']) : null;
                    $hashTagVideoData[$key]['user_name'] = $singleUserData[0]['username'];
                    $hashTagVideoData[$key]['user_id'] = $singleUserData[0]['id'];
                    $hashTagVideoData[$key]['views'] = $value['video']['views'];
                    $hashTagVideoData[$key]['location'] = $value['video']['location'];
                    $hashTagVideoData[$key]['video_id'] = $value['video']['id'];
                    $hashTagVideoData[$key]['video_name'] = (isset($value['video']['video_name'])) ? $value['video']['video_name'] : null;
                    $hashTagVideoData[$key]['video_title'] = $value['video']['video_title'];
                    $hashTagVideoData[$key]['likes'] = $this->getVideoActivityCount($value['id'],'like');
                    $hashTagVideoData[$key]['like_by_me'] = $this->byMe($value['id'],'like',\Auth::user()->id);
                    $hashTagVideoData[$key]['shares'] = $value['video']['shares'];
                    $hashTagVideoData[$key]['saved_by_me'] = $this->byMe($value['id'],'save',\Auth::user()->id);
                    $hashTagVideoData[$key]['reported'] = $this->videoReported($value['video']['id']);
                }                    
                $finalData['hashTagData'] = $hashTagVideoData;
                
                $this->response['data'] = $finalData;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Get Hashtag Videos Successfully";
            }else{
                $this->response['data'] = $finalData;
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

    public function TrendingVideo(Request $request)
    {
        $videoData = Video::select('id','user_id','video_name','video_title')
                    ->with('users:id,username')                        
                    ->get()->toArray();
                
        if(!empty($hashTagVideo)){
            foreach ($hashTagVideo as $key => $value) {                                 
                //$finalData[$key]['video_title'] = str_replace('#', '', $value['video']['video_title']);
                $finalData[$key]['id'] = $value['video']['id'];
                $finalData[$key]['video_name'] = (isset($value['video']['video_name'])) ? $value['video']['video_name'] : null;
            }                    
            $this->response['data'] = $finalData;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Get Hashtag Videos Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['not_found'];
            $this->response['message'] = "Video Not Found";
        }
            
        return response()->json($this->response);
    }    

    public function searchTopPeopleHashTag(Request $request)
    {
        $rules = [
            'keyword' => 'required',            
        ];        

        if($this->ApiValidator($request->all(), $rules)) {     
                $keyword = $request->keyword;

                //Get People Data
                $getPeople = User::where(function ($query) use ($keyword) {
                    $query->where('username', 'like', '%' . $keyword . '%');
                })->get()->toArray();

                if(!empty($getPeople)){
                    foreach ($getPeople as $key => $value) {                    
                        $finalData['people'][$key]['id'] = $value['id'];
                        $finalData['people'][$key]['first_name'] = $value['first_name'];
                        $finalData['people'][$key]['last_name'] = $value['last_name'];
                        $finalData['people'][$key]['username'] = $value['username'];
                        $finalData['people'][$key]['profile_picture'] = (isset($value['profile_picture'])) ? config('app.url').'/public/admin_theme/assets/profile_picture/'.$value['profile_picture'] : null;
                        $finalData['people'][$key] = $this->convertNullToString($finalData['people'][$key]);
                        $finalData['people'][$key]['type'] = 'people';
                    }
                
                }
                $finalData['people'] = isset($finalData['people']) ? $finalData['people'] : array();

                //Get HashTags Data
                $getHashTag = Hashtag::select('hashtag')->where(function ($query1) use ($keyword) {
                                    $query1->where('hashtag', 'like', '%' . $keyword . '%');
                                })->groupBy('hashtag')->get()->toArray();

                if(!empty($getHashTag)){
                    foreach ($getHashTag as $key => $value) {
                        //$finalData[$key]['video_title'] = str_replace('#', '', $value['video']['video_title']);
                        $finalData['hashtag'][$key]['id'] = Hashtag::where('hashtag',$value['hashtag'])->value('id');
                        $finalData['hashtag'][$key]['hashtag'] = $value['hashtag'];
                        $finalData['hashtag'][$key]['videos'] = Hashtag::where('hashtag',$value['hashtag'])->count();
                        $finalData['hashtag'][$key]['type'] = 'hashtag';
                    }
                }
                $finalData['hashtag'] = isset($finalData['hashtag']) ? $finalData['hashtag'] : array();
                
                if(empty($finalData['hashtag']) && !empty($finalData['people'])){
                    $finalData['all'] = array_merge($finalData['people']);
                }else if(!empty($finalData['hashtag']) && empty($finalData['people'])){
                    $finalData['all'] = array_merge($finalData['hashtag']);
                }else if(!empty($finalData['hashtag']) && !empty($finalData['people'])){
                    $finalData['all'] = array_merge($finalData['hashtag'],$finalData['people']);                    
                }
                
                $finalData['all'] = isset($finalData['all']) ? $finalData['all'] : array();

                if(!empty($finalData)){
                    $this->response['data'] = $finalData;
                    $this->response['status'] = $this->statusArr['success'];
                    $this->response['message'] = "Get People Successfully";
                }else{
                    $this->response['data'] =  null;
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
}