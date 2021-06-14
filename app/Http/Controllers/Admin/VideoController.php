<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.video.list');
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
        $videoData = Video::where('id',$id)->first();
        @unlink(public_path("assets/videos/".$videoData->video_name));        
        $videoData->delete();

        if(request()->ajax()){
            return response()->json(['message'=>"Deleted Successfully."]);
        }else{
            return redirect()->route('video.index');
        }
    }
    public function listing(Request $request)
    {                
        extract($this->DTFilters($request->all()));
        $category = Video::where('user_id',$request->parent_id);
        
        if($search != ''){
            $category->where(function($query) use ($search){
                $query->where("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%");
            });
        }
        
        $count = $category->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = [];

        $category = $category->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();
        $count_no = 1;

        foreach ($category as $categoryData){                    
            $records['data'][] = [
                'id' => $count_no++,
                'video_name' => $categoryData->video_name,
                'video_title' => $categoryData->video_title,
                'views' => $categoryData->views,
                'likes' => $categoryData->likes,
                'shares' => $categoryData->shares,
                'save' => $categoryData->save,
                'report' => $categoryData->report,
                'location' => $categoryData->location,                
                'status' => view('admin.shared.action')->with(['status' => $categoryData->status,'id' => $categoryData->id,'slug' => $categoryData->slug, 'routeName' => 'video','statusshow'=>true,'edit'=>false,'delete'=>false,'view'=>false,'user_view'=>false,'user_view'=>false,'user'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),                
                'date' => $categoryData->created_at->format('d-m-Y'),
                'action' => view('admin.shared.action')->with(['id' => $categoryData->id, 'routeName' => 'video','view'=>false,'edit'=>false,'user_view'=>false,'user_view'=>false,'user'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),
            ];
        }
        return $records;
    }

    //Change Status
    public function changeStatus(Request $request)
    {
        $result = Video::where('id',$request->get('id'))->update(['status' => $request->get('status')]);
        
        if($result == true)
            return response()->json(['status' => 'true','message'=>"Status has been Changed."]);
        else
            return response()->json(['status' => 'false','message'=>"Something went wrong !"]);
    }
}
