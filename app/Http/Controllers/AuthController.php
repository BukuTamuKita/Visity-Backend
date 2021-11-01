<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Host;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['loginAdmin', 'register', 'loginHost']]);

    }

    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|string',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);

            if($request->role == "host"){
                $this->validate($request, [
                    'nip' => 'required|string|unique:hosts',
                    'position' => 'required|string',
                ]);
                Host::create([
                    'name' => $user->name,
                    'nip' => $request->nip,
                    'position' => $request->position,
                    'user_id' => $user->id
                ]);
            }
            
            return response()->json([$user], 201);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 409,
                'message' => 'Conflict',
                'description' => 'User Registration Failed!',
                'exception' => $e
            ], 409);
        }
    }

    public function loginAdmin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        
        
            if ($token= Auth::attempt($credentials)) {
                $email = User::where('email',$request->email)->firstOrFail();
        
                $role = $email->role;
                if($role == "admin"){
                    return $this->respondWithToken($token);
                } else{
                    return response()->json([
                        'code' => 401,
                        'message' => 'Unauthorized',
                        'description' => 'User unauthorized.',
                    
                    ], 401);
                }
           
            } 
     
        else {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
                'description' => 'User unauthorized.',
         
            ], 401);
        }
    }


    public function loginHost(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $credentials = $request->only(['email', 'password']);
        
            if ($token= Auth::attempt($credentials)) {
                $email = User::where('email',$request->email)->firstOrFail();
    
                $role = $email->role;
                if($role == "host"){
                    return $this->respondWithToken($token);
                } else{
                    return response()->json([
                        'code' => 401,
                        'message' => 'Unauthorized',
                        'description' => 'User unauthorized.',
                    ], 401);
                    
                }
            } 
        // } 
        else {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
                'description' => 'User unauthorized.',
                
            ], 401);
        }

       

    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function me()
    {
        return new UserResource(Auth::user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
