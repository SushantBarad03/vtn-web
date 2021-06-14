<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Video;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.report.list');
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
    public function show($user_id)
    {
        return view('admin.pages.report.report_user',compact('user_id'));
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

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $report = Report::where('id','>',0);

        // $report = \DB::table('report')
        //         ->join('videos', 'report.video_id', '=', 'videos.id')
        //         //->join('users', 'users.id', '=', 'report.user_id')
        //         ->select('report.id', 'videos.video_name')
        //         ->get();



        // $articles =DB::table('articles')
        //         ->join('categories', 'articles.id', '=', 'categories.id')
        //         ->join('users', 'users.id', '=', 'articles.user_id')
        //         ->select('articles.id','articles.title','articles.body','users.username', 'category.name')
        //         ->get();
        
        if($search != ''){
            $report->where(function($query) use ($search){
                $query->where("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%");
            });
        }
        
        $count = $report->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = [];

        $report = $report->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();
        $count_no = 1;
        
        foreach ($report as $reportData){
            $records['data'][] = [
                'id' => $count_no++,
                'name' => Video::where('id',$reportData->video_id)->value('video_name'),
                'status' => view('admin.shared.action')->with(['status' => $reportData->status,'id' => $reportData->id,'slug' => $reportData->slug, 'routeName' => 'user','statusshow'=>true,'edit'=>false,'delete'=>false,'view'=>false,'user_view'=>false,'user'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),                
                'date' => \Carbon\Carbon::parse($reportData->created_at)->format('d-m-Y'),
                'action' => view('admin.shared.action')->with(['id' => $reportData->id, 'routeName' => 'user','view'=>false,'edit'=>false,'user_view'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),
            ];
        }
        return $records;
    }

    public function userListing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $users = Report::where('user_id','>',$request->user_id);

        if($search != ''){
            $users->where(function($query) use ($search){
                $query->where("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%");
            });
        }
        
        $count = $users->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = [];

        $users = $users->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();
        $count_no = 1;
        
        foreach ($users as $userData){
            $records['data'][] = [
                'id' => $count_no++,
                'name' => User::where('id',$userData->user_id)->value('username'),
                'message' => $userData['reason'],
                'status' => view('admin.shared.action')->with(['status' => $userData->status,'id' => $userData->id,'slug' => $userData->slug,'routeName' => 'user','statusshow'=>true,'edit'=>false,'delete'=>false,'view'=>false,'user_view'=>false,'user'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),
                'date' => \Carbon\Carbon::parse($userData->created_at)->format('d-m-Y'),
                'action' => view('admin.shared.action')->with(['id' => $userData->id, 'routeName' => 'user','view'=>false,'user_view'=>false,'edit'=>false,'user'=>false,'statusApproved'=>false,'statusDecline'=>false])->render(),
            ];
        }
        return $records;
    }
}
