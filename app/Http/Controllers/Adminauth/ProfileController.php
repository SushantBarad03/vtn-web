<?php

namespace App\Http\Controllers\Adminauth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Session;
use Hash;
use Auth;

class ProfileController extends Controller
{
    public function index()
    {
    	return view('admin.pages.ChangePassword');
    }
    public function update(Request $request)
    {
    	$rules = [
            'name' => 'required',           
            'new_password' => 'confirmed',
        ];

        $this->validateForm($request->all(), $rules);
        
        // The passwords matches
        //Current password and new password are same
        if(!empty($request->get('new_password')) || !empty($request->get('new_password_confirmation'))){
        	
            if(!empty($request->get('password'))){            	
    	    	if (!(Hash::check($request->get('password'), Auth::user()->password))) {
    	            return redirect()->back()->with('msg', 'Your Current Password Does Not Matches With The Password You Provided. Please try again.');
    	       	}
    	    }else{
                return redirect()->back()->with('msg', 'Please Enter Old Password');
            }
            
	        if(!strcmp($request->get('new_password'), $request->get('new_password_confirmation')) == 0){
	            return redirect()->back()->with("same_pass_msg","New Password cannot be same as your current password. Please choose a different password.");
	        }else{
		        //Change Password
		        Admin::where('id',Auth::user()->id)->update(['password' => Hash::make($request->get('new_password'))]);               
                Admin::where('id',$request->profile_id)->update([
                    'name' => $request->name,                    
                ]);
		    }
	    }        
        Session::flash('success', 'Profile Changed Successfully.');
        return redirect()->route('index');
    }
}
