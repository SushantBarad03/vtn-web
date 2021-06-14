<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtherPagesController extends Controller
{
    public function privacyPolicy()
    {
    	return view('frontend.privacy_policy');
    }

    public function termsAndCondition()
    {
    	return view('frontend.terms_and_condition');
    }
}
