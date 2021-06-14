<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;

class DashboardController extends Controller
{
	public function index()
    {
    	$data['UserCount'] = User::where('status','1')->count();
    	$data['VideoCount'] = Video::where('status','1')->count();
    	return view('admin.pages.home',compact('data'));
    }
}