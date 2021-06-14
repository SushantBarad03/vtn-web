<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\Distict;
use App\Models\City;

class LocationController extends Controller
{
   public function getCountry()
    {
        $country = Country::get(['id','country_name'])->toArray();
        if(!empty($country)) {
            $this->response['data'] = $country;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Get Country Name";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Country Not Available";
        }
        return response()->json($this->response);
    }

    public function getState(Request $request)
    {
        $rules = [
            'country_id' => 'required',
        ];  

        if($this->ApiValidator($request->all(), $rules)) {
            $state = State::where('country_id',$request->country_id)->get(['id','state_name'])->toArray();
        
            if(!empty($state)) {
                $this->response['data'] = $state;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Get State Name";
            }else{
                $this->response['data'] = null;
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "State Not Available";
            }
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }
    
    public function getDistict(Request $request)
    {
        $rules = [
            'state_id' => 'required',
        ]; 
        
        if($this->ApiValidator($request->all(), $rules)) {
            $city = Distict::where('state_id',$request->state_id)->get(['id','district_name'])->toArray();

            if(!empty($city)) {
                $this->response['data'] = $city;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Get City Name";
            }else{
                $this->response['data'] = null;
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "City Not Available";
            }
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);                 
    }

    public function getCity(Request $request)
    {
        $rules = [
            'district_id' => 'required',
        ]; 
        
        if($this->ApiValidator($request->all(), $rules)) {
            $city = City::where('district_id',$request->district_id)->get(['id','city_name'])->toArray();

            if(!empty($city)) {
                $this->response['data'] = $city;
                $this->response['status'] = $this->statusArr['success'];
                $this->response['message'] = "Get City Name";
            }else{
                $this->response['data'] = null;
                $this->response['status'] = $this->statusArr['not_found'];
                $this->response['message'] = "City Not Available";
            }
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);                 
    }
}
