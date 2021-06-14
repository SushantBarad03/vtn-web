<?php
use Illuminate\Support\Facades\Route;

/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/','App\Http\Controllers\HomeController@index')->name('/');

Auth::routes();

//Admin Login
Route::get('admin','App\Http\Controllers\Adminauth\LoginController@showLoginForm')->name('admin.login');
Route::post('admin','App\Http\Controllers\Adminauth\LoginController@login');

Route::get('logout', 'App\Http\Controllers\Adminauth\LoginController@logout')->name('admin.logout');

Route::group(['middleware'=> 'auth:admin','prefix' => 'admin'],function(){
    Route::get('home','App\Http\Controllers\Admin\DashboardController@index')->name('index');

    //Profile Route
	Route::get('profile','App\Http\Controllers\Admin\ProfileController@index')->name('profile');
	Route::post('change-profile','App\Http\Controllers\Admin\ProfileController@update')->name('change-profile');

	//User Controller Module
	Route::get('user/listing', 'App\Http\Controllers\Admin\UserController@listing')->name('user.listing');
	Route::post('user/status','App\Http\Controllers\Admin\UserController@changeStatus')->name('user.status');
	Route::resource('user', 'App\Http\Controllers\Admin\UserController');

	//Videos Controller Module
	Route::get('video/listing', 'App\Http\Controllers\Admin\VideoController@listing')->name('video.listing');
	Route::post('video/status','App\Http\Controllers\Admin\VideoController@changeStatus')->name('video.status');
	Route::resource('video', 'App\Http\Controllers\Admin\VideoController');

	//Videos Approve Controller Module
	Route::get('approve_video/listing', 'App\Http\Controllers\Admin\ApproveVideoCotroller@listing')->name('approve_video.listing');
	Route::post('approve_video/status','App\Http\Controllers\Admin\ApproveVideoCotroller@approveStatus')->name('approve_video.status');
	Route::resource('approve_video', 'App\Http\Controllers\Admin\ApproveVideoCotroller');

	//Report Controller Module
	Route::get('report/listing', 'App\Http\Controllers\Admin\ReportController@listing')->name('report.listing');
	Route::get('report/userListing', 'App\Http\Controllers\Admin\ReportController@userListing')->name('report.userListing');
	Route::post('report/status','App\Http\Controllers\Admin\ReportController@changeStatus')->name('report.status');
	Route::resource('report', 'App\Http\Controllers\Admin\ReportController');

	//User Controller Module
	Route::get('complainant_data/listing', 'App\Http\Controllers\Admin\UserReportController@listing')->name('complainant_data.listing');
	Route::get('complainant_data/userListing', 'App\Http\Controllers\Admin\UserReportController@userListing')->name('complainant_data.userListing');
	Route::post('complainant_data/status','App\Http\Controllers\Admin\UserReportController@changeStatus')->name('user.report.status');
	Route::resource('complainant_data', 'App\Http\Controllers\Admin\UserReportController');
});

Route::get('privacy-policy','App\Http\Controllers\OtherPagesController@privacyPolicy')->name('privacy-policy');
Route::get('terms-and-condition','App\Http\Controllers\OtherPagesController@termsAndCondition')->name('terms-and-condition');

//share video
Route::get('{id}/video', 'App\Http\Controllers\VideoShareController@index');

// Delete Notification Cronjob
Route::get('admin/delete-notification','App\Http\Controllers\Admin\VideoShareController@delteNotification');

//Reset Password
route::get('admin-password/request','App\Http\Controllers\Adminauth\ForgotPasswordController@showlinkrequestform')->name('admin.auth.password.request');
route::post('admin-password/email','App\Http\Controllers\Adminauth\ForgotPasswordController@sendresetlinkemail')->name('admin.auth.password.email');
route::post('admin-password/newpassword','App\Http\Controllers\Adminauth\ForgotPasswordController@newpassword')->name('admin.auth.password.newpassword');

//Reset Password
Route::post('admin-password/reset','App\Http\Controllers\Adminauth\ResetPasswordController@reset');
route::get('admin-password/reset/{token}','App\Http\Controllers\Adminauth\ResetPasswordController@showresetform')->name('password.reset');