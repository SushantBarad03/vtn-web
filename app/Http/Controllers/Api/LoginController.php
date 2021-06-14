<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use App\Models\Distict;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class LoginController extends Controller
{
    //Login with facebook and gmail
    public function login(Request $request)
    {
    	$rules = [
            'fcm_token' => 'required',
        ];

        if($this->ApiValidator($request->all(), $rules)) {
        	$data = $request->all();

        	if(!empty($data['google_id'])){
        		$user = User::where('google_id',$data['google_id'])->first();
        	}else if(!empty($data['fb_id'])){
        		$user = User::where('fb_id',$data['fb_id'])->first();
        	}

        	if(!empty($user)){
                $first_name = isset($data['first_name']) ? $data['first_name'] : null;
                $last_name = isset($data['last_name']) ? $data['last_name'] : null;
                $username = $first_name.' '.$last_name;
                $mobile_no = isset($data['mobile_no']) ? $data['mobile_no'] : null;
                $email = isset($data['email']) ? $data['email'] : null;

                if (!empty($user->profile_picture)) {
                    if(!str_starts_with($user->profile_picture,'profile_picture')){
                        $profile_picture = isset($data['profile_picture']) ? $data['profile_picture'] : null;
                    }else{
                        $profile_picture = $this->checkProfilePicture($user->profile_picture);
                    }
                    
                    $user->update(['first_name' => $first_name,'last_name' => $last_name,'mobile_no' => $mobile_no, 'email' => $email, 'profile_picture' => $profile_picture,'fcm_token' => $data['fcm_token']]);
                }

    		}else{
				$user = new User();
				$user->first_name = isset($data['first_name']) ? $data['first_name'] : null;
				$user->last_name = isset($data['last_name']) ? $data['last_name'] : null;
				$user->username = $user->first_name.' '.$user->last_name;
				$user->mobile_no = isset($data['mobile_no']) ? $data['mobile_no'] : null;
				$user->email = isset($data['email']) ? $data['email'] : null;
				$user->profile_picture = isset($data['profile_picture']) ? $data['profile_picture'] : null;
				$user->fb_id = isset($data['fb_id']) ? $data['fb_id'] : null;
				$user->google_id = isset($data['google_id']) ? $data['google_id'] : null;
                $user->fcm_token = isset($data['fcm_token']) ? $data['fcm_token'] : null;
				$user->save();
				$user->refresh();
    		}
			Auth::login($user);

    		$tokenResult = $user->createToken('Laravel Password Grant Client');                    
            $token = $tokenResult->token;                    
            $token->expires_at = \Carbon\Carbon::now()->addMinutes(10);
            $token->save();

            $this->response['data'] = $this->convertNullToString($user->toArray());
            $this->response['access_token'] = response()->json([
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => \Carbon\Carbon::parse(
                        $tokenResult->token->expires_at
                    )->toDateTimeString(),
                    'basic_info_status' => $user->basic_info_status,
                ]);                        
        
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Login Successfully";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->response;
            $this->response['message'] = "Parameter Validate";
        }
        return response()->json($this->response);
    }

    public function currentLocation(Request $request)
    {     
        // $data = Location::get($request->ip);

        $client = new \GuzzleHttp\Client();
        $res = $client->get('https://api.bigdatacloud.net/data/reverse-geocode-client?latitude='.$request->latitude.'&longitude='.$request->longitude.'&localityLanguage=en');
        echo $res->getStatusCode(); // 200
        $curlData = json_decode($res->getBody());
        // City Id
        $curlCity = $curlData->locality;

        \DB::enableQueryLog();       

        // $users = \DB::table('cities')
        //         ->where('city_name', '==', $curlCity.'%')
        //         // ->where($field, 'LIKE', $letter.'%');
        //         ->get();

        // $users = City::where('city_name', 'LIKE', $curlCity . '%')
        //     ->selectRaw('*, LEFT (city_name, 1) as first_char')
        //     ->get();

        $users = City::where('city_name','LIKE','%'.$curlCity.'%')->orWhere('city_name','LIKE','%'.$curlCity.'%')->get()->toArray();

        // $users = City::where('city_name', 'like', '%'.$curlCity.'%')->get();
        // dd($users)        ;
        $query = \DB::getQueryLog();
        print_r(end($query));die();

        dd($dd);
        $cityId = City::where('id','>',0);
        dd($cityId);
        $cityId->where(function($query) use ($curlCity){
            $query->where("city_name","like","%$curlCity%");
        });

        dd($cityId);
        $cityId->get()->toArray();
        dd($cityId);

        print_r($cityId);
        $query = \DB::getQueryLog();
        print_r(end($query));die();
        print_r($cityId);die();
        // District Id
        $getDistrictId = City::where('city_name',$data->cityName)->Value('district_id');
        // State Id
        $stateId = Distict::where('id',$getDistrictId)->Value('state_id');
        // Country Id
        $countryId = State::where('id',$stateId)->Value('country_id');

        if(!empty( $getDistrictId)) {
            User::where('id',\Auth::user()->id)->update([
                'district' => $getDistrictId,
                'state' => $stateId,
                'country' => $countryId,
                'city'  => $getDistrictId,
                //'slug' => Story::getSlugForCustom($request->title),
            ]);
            $this->response['data'] = $getDistrictId;
            $this->response['status'] = $this->statusArr['success'];
            $this->response['message'] = "Store Id";
        }else{
            $this->response['data'] = null;
            $this->response['status'] = $this->statusArr['validation'];
            $this->response['message'] = "City Not Found";
        }
        return response()->json($this->response);
    }

    public function testAws(Request $request)
    {
        $path = $request->file('image')->store('images','s3');

        return $path;
    }
}