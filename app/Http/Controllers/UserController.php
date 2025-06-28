<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function register(StoreUserRequest $request){

        $user = User::create($request->all());
        return $user;
    }

    public function login(Request $request){
        //dd("User logging");
           $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string']
        ]);

        //validate user

        if(!Auth::attempt($request->only('email', 'password'))){
             throw ValidationException::withMessages([
                'email'=> ['Incorrect credentials']
            ]);
        }

             // Get the authenticated user
        $user = Auth::user();

         $client = DB::table('oauth_clients')->where('id','0197b637-4956-73d1-9cb4-9cd9036cbf27')->firstOrFail();

        // dd($client);
     

        $http = new Client();
        try{
            $url = url('/oauth/token');
            $response = $http->post($url, [

                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $client->id,
                    'client_secret' => $client->secret,
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ]
            ]); 
            
              $tokenData = json_decode((string) $response->getBody(), true);

              return response()->json($tokenData);
            
    }catch(ValidationException $e){
            return response()->json([
                'error'=> $e->getMessage()
            ]);
    }
    }

}
