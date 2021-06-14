<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Login
Route::post('login','App\Http\Controllers\Api\LoginController@login');
Route::post('test-s3-aws','App\Http\Controllers\Api\LoginController@testAws');

Route::group(['middleware' => ['auth:api'],'prefix' => 'user'], function(){
	
	//Set location
	Route::post('currentlocation','App\Http\Controllers\Api\LoginController@currentLocation');

	//Home
	Route::post('home','App\Http\Controllers\Api\HomeController@index');
	Route::post('report','App\Http\Controllers\Api\HomeController@reportVideo');
	// Route::post('savevideo','App\Http\Controllers\Api\HomeController@saveVideo');
	Route::post('videoactivity','App\Http\Controllers\Api\HomeController@videoActivity');
	//Route::post('videocomment','App\Http\Controllers\Api\HomeController@videoComment');
	Route::post('reportcomment','App\Http\Controllers\Api\HomeController@reportComment');

	//video
	Route::post('addvideo','App\Http\Controllers\Api\VideoController@addVideo')->name('add-video');
	Route::post('suggestion','App\Http\Controllers\Api\VideoController@getTagUserSuggestion');
	Route::post('addvideoview','App\Http\Controllers\Api\VideoController@addVideoView');
	Route::post('deletevideo','App\Http\Controllers\Api\VideoController@deleteVideo');
	Route::post('addvideocomment','App\Http\Controllers\Api\VideoController@addVideoComment');
	Route::post('deletevideocomment','App\Http\Controllers\Api\VideoController@deleteVideoComment');
	Route::post('video','App\Http\Controllers\Api\VideoController@video');

	//Profile
	Route::post('getuserprofile','App\Http\Controllers\Api\ProfileController@getUserProfile');
	Route::post('updateuserprofile','App\Http\Controllers\Api\ProfileController@editUserProfile');
	Route::post('storelocation','App\Http\Controllers\Api\ProfileController@storeLocation');
	Route::post('reportuser','App\Http\Controllers\Api\ProfileController@reportUser');

	Route::post('getprofile','App\Http\Controllers\Api\ProfileController@getProfile');

	Route::post('follow','App\Http\Controllers\Api\ProfileController@follow');
	Route::post('followers','App\Http\Controllers\Api\ProfileController@myFollowers');
	Route::post('followings','App\Http\Controllers\Api\ProfileController@myFollowings');

	//Search
	Route::post('searchdata','App\Http\Controllers\Api\SearchController@searchData');
	Route::post('gethashtagvideos','App\Http\Controllers\Api\SearchController@getHashTagVideos');

	Route::post('search','App\Http\Controllers\Api\SearchController@searchTopPeopleHashTag');

	//Settings
	Route::post('changesettings','App\Http\Controllers\Api\ProfileController@changeSettings');

	//Suggestion
	Route::post('suggestion','App\Http\Controllers\Api\VideoController@getSuggestion');	

	//BackGround Music
	Route::post('background_music','App\Http\Controllers\Api\VideoController@getBackgroundMusic');

	//change Location
	Route::post('change-location','App\Http\Controllers\Api\ProfileController@changeLocation');	

	//Notification
	Route::post('get-notification','App\Http\Controllers\Api\NotificationController@getNotification');

	//Country State District	
	Route::post('getcountry','App\Http\Controllers\Api\LocationController@getCountry');
	Route::post('getstate','App\Http\Controllers\Api\LocationController@getState');
	Route::post('getdistict','App\Http\Controllers\Api\LocationController@getDistict');
	Route::post('getcities','App\Http\Controllers\Api\LocationController@getCity');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});