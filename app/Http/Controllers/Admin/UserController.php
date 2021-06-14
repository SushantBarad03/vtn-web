<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($parent_id = 0)
    {
        return view('admin.pages.user.list',compact('parent_id'));
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
    public function show($parent_id)
    {
        $videoData = Video::where('user_id',$parent_id)->get(['id','video_name','video_title','views','likes','shares','save','report','location'])->toArray();

        return view('admin.pages.video.list',compact('videoData','parent_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.pages.user.edit',compact('user'));
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

    public function listing(Request $request)
    {             
        extract($this->DTFilters($request->all()));
        $category = User::where('id','>',0);
        
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

            if (!isset($categoryData->profile_picture)) 
            {
                $categoryData->profile_picture = "../download.jpg";
            }  
        
            $records['data'][] = [
                'id' => $count_no++,
                'name' => $categoryData->first_name. ' '.$categoryData->last_name,
                'username'=> $categoryData->username,
                'profile_picture' => '<img style="width:80px; height:80px;" src="' . asset('admin_theme/assets/profile_picture/' . $categoryData->profile_picture) . '" alt="newsData Image">',

                'status' => view('admin.shared.action')->with(['status' => $categoryData->status,'id' => $categoryData->id,'slug' => $categoryData->slug, 'routeName' => 'user','statusshow'=>true,'edit'=>false,'delete'=>false,'view'=>false,'user_view'=>false,'user'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),                
                'date' => \Carbon\Carbon::parse($categoryData->created_at)->format('d-m-Y'),
                'action' => view('admin.shared.action')->with(['id' => $categoryData->id, 'edit' => false,'routeName' => 'user','user_view'=>false,'user'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),

            ];
        }
        return $records;
    }

    //Change Status
    public function changeStatus(Request $request)
    {
        $result = User::where('id',$request->get('id'))->update(['status' => $request->get('status')]);
        
        if($result == true)
            return response()->json(['status' => 'true','message'=>"Status has been Changed."]);
        else
            return response()->json(['status' => 'false','message'=>"Something went wrong !"]);
    }
}
