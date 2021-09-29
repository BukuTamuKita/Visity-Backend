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
    // /**
    //  * Create a new AuthController instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }

    // /**
    //  * Get a JWT via given credentials.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function login()
    // {
    //     $credentials = request(['email', 'password']);

    //     if (! $token = auth()->attempt($credentials)) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     return $this->respondWithToken($token);
    // }

    // /**
    //  * Get the authenticated User.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function me()
    // {
    //     return response()->json(auth()->user());
    // }

    // /**
    //  * Log the user out (Invalidate the token).
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function logout()
    // {
    //     auth()->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    // /**
    //  * Refresh a token.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }

    // /**
    //  * Get the token array structure.
    //  *
    //  * @param  string $token
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth()->factory()->getTTL() * 60
    //     ]);
    // }
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['loginAdmin', 'register', 'loginHost']]);
        // return auth()->shouldUse('api');
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
                Host::create([
                    'name' => $user->name,
                    'nip' => $request->name,
                    'position' => $request->name,
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
        
        //  if($role == "host"){
            if ($token= Auth::attempt($credentials)) {
                $email = User::where('email',$request->email)->firstOrFail();
        // $id = User::find($email);
                $role = $email->role;
                if($role == "admin"){
                    return $this->respondWithToken($token);
                } else{
                    return response()->json([
                        'code' => 401,
                        'message' => 'Unauthorized',
                        'description' => 'User unauthorized.',
                        // 'description' => $role,
                        // 'notes' => $role,
                    ], 401);
                    
                }
                // return response()->json([
                //     'code' => 401,
                //     'message' => 'Unauthorized',
                //     // 'description' => 'User unauthorized.',
                //     'description' => $role,
                //     // 'notes' => $role,
                // ], 401);
            } 
        // } 
        else {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
                'description' => 'User unauthorized.',
                // 'description' =>,
                // 'notes' => $role,
            ], 401);
        }
    }

    // public function findByEmail(Request $request){
    //     $email = User::where('email',$request->email)->firstOrFail();

    // }

    public function loginHost(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        // $email = User::where('email',$request->email)->firstOrFail();
        // // $id = User::find($email);
        // $role = $email->role;
        // $role = User::where('email',$request->email)->get('role');
        $credentials = $request->only(['email', 'password']);
        // $token = null;

        // if($role == "host"){
            if ($token= Auth::attempt($credentials)) {
                $email = User::where('email',$request->email)->firstOrFail();
        // $id = User::find($email);
                $role = $email->role;
                if($role == "host"){
                    return $this->respondWithToken($token);
                } else{
                    return response()->json([
                        'code' => 401,
                        'message' => 'Unauthorized',
                        'description' => 'User unauthorized.',
                        // 'description' => $role,
                        // 'notes' => $role,
                    ], 401);
                    
                }
                // return response()->json([
                //     'code' => 401,
                //     'message' => 'Unauthorized',
                //     // 'description' => 'User unauthorized.',
                //     'description' => $role,
                //     // 'notes' => $role,
                // ], 401);
            } 
        // } 
        else {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
                'description' => 'User unauthorized.',
                // 'description' =>,
                // 'notes' => $role,
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
