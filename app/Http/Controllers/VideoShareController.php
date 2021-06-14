<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\City;
use App\Models\Distict;

class VideoShareController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $videoData = Video::select('id','user_id','video_name','video_title','views','location','district_id','created_at')
                ->where('status','1')
                ->where('id',$request->id)
                ->with('users:id,username')
                ->get()->toArray();
        
        if(!empty($videoData)){
            foreach ($videoData as $key => $value) {
                    $finalData['username'] = $value['users']['username'];
                    $finalData['views'] = $value['views'];
                    $finalData['video_name'] = (isset($value['video_name'])) ? config('app.url').'/public/admin_theme/assets/videos/'.$value['video_name'] : null;
                    $finalData['video_title'] = str_replace('#', '', $value['video_title']);
                    $finalData['city'] = City::where('id',$value['location'])->value('city_name');
                    $finalData['distict'] = Distict::where('id',$value['district_id'])->value('district_name');
                    $finalData['created_at'] = \Carbon\Carbon::parse($value['created_at'])->format('d-m-Y');
                }
        }else{
            return view('frontend.not_found_video');
        }
        return view('frontend.video_share',compact('finalData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('frontend.video_share');
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
}
