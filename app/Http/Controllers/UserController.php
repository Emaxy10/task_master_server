<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Models\Subscription;
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
        $user->assignRole('user');
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
        $user_sub = $user->subscription->plan;
        $user_roles = $user->roles;
     

        $http = new Client();
        try{
            $url = url('/oauth/token');
            $response = $http->post($url, [

                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('PASSWORD_CLIENT_ID'),
                    'client_secret' => env('PASSWORD_CLIENT_SECRET'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ]
            ]); 
            
              $tokenData = json_decode((string) $response->getBody(), true);

            //   return response()->json($tokenData);

                return response()->json([
                    'user' => $user,
                    // 'subscription' =>$user_sub,
                   
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'],
                    'token_type' => $tokenData['token_type'],
                    'expires_in' => $tokenData['expires_in'],
                ]);
            
    }catch(ValidationException $e){
            return response()->json([
                'error'=> $e->getMessage()
            ]);
    }
    }

    public function getUser(){
        $users = User::whereHas('subscription', function ($query) {
            $query->where('plan', 'team');
        })->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    public function addTeamMember(User $user){

        $authId = Auth::id();

        // 🚫 Prevent assigning yourself
        if ($user->id === $authId) {
            return response()->json([
                'status' => 400,
                'message' => 'You cannot assign yourself as your own supervisor.',
            ], 400);
        }

            // 🚦 Check if user already has a supervisor
        if (!is_null($user->supervisor_id)) {
            return response()->json([
                'status' => 400,
                'message' => 'This user already has a supervisor assigned'
            ], 400);
        }
        
        
        $user->supervisor_id = $authId;

        // Assign subscription plan "team"
            if ($user->subscription) {
                // if subscription already exists, update it
                $user->subscription->plan = 'team';
                $user->subscription->is_active = true;
                $user->subscription->save();
            } else {
                // if no subscription exists, create one
                $user->subscription()->create([
                    'plan' => 'team',
                    'is_active' => true
                ]);
            }



        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Supervisor assigned successfully'
        ]);
                
    }

    public function searchEmail($search){
        $user = User::where('email', 'like', "%$search%")->first();

        return response()->json($user);
    }

    public function getTeamMembers()
        {
            $supervisor = Auth::user();

            // Only fetch members who belong to this supervisor
            $members = $supervisor->members;

            return response()->json([
                'status' => 200,
                'members' => $members
            ]);
        }


    public function removeMember($id)
    {
        $member = User::findOrFail($id);

        // Ensure the member belongs to the current supervisor
        if ($member->supervisor_id !== Auth::id()) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Remove supervisor assignment
        $member->supervisor_id = null;
        $member->save();

        return response()->json([
            'status' => 200,
            'message' => 'Member removed successfully'
        ]);
    }


    

}
