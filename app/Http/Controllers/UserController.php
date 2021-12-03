<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\Host;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public $order_table = 'users';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::when([$this->order_table, $this->orderBy], \Closure::fromCallable([$this, 'queryOrderBy']))
        ->when($this->limit, \Closure::fromCallable([$this, 'queryLimit']));

        return UserResource::collection($user);
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
        $this->validate($request, [
            'name' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|string',
            'nip' => 'nullable|string|unique:hosts',
            'position' => 'nullable|string',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
                'photo' => $this->uploadImage($request)
            ]);

            if($request->role == "host"){
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
                'description' => 'User Creation Failed!',
                'exception' => $e
            ], 409);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return new UserResource(User::findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'User ' . $id . ' not found.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'email' => 'email|unique:users',
            'password' => 'string',
        ]);

        try {
            $user = User::findOrFail($id);
            // $user->update($request->all());
            if(!empty($request->email)){
                $user->update([
                    'email' => $request->email,
                ]);
            } else {
                $user->update([
                    'password'=> bcrypt($request->password)]
                );
            }
           

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'User ' . $id . ' not found.'
            ], 404);
        }
    }

    public function resetPassword($id){
        try {
            $user = User::findOrFail($id);
            // $user->update($request->all());

            $user->update([
                'password'=> bcrypt('password')]
            );
            
            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'User ' . $id . ' not found.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {
            User::findOrFail($id)->delete();

            return response()->json([], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'User ' . $id . ' not found.'
            ], 404);
        }
    }

    public function saveToken(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        $user->update(['device_token'=>$request->token]);
        return response()->json(['token saved successfully.']);
    }

    public function logoutToken(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        $user->update(['device_token'=>null]);
        return response()->json(['token deleted successfully.']);
    }

    public function uploadImage(Request $request){
        // $user = User::find(Auth::id());
        if($request->photo != null){
            $validator = Validator::make($request->all(), [
                'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if($validator->fails()){
                return null;
            }
            $file = $request->file('photo');
            $path = 'storage/' . basename( $_FILES['photo']['name']);
            // if($user->photo != null)
                // File::delete($user->photo);
            move_uploaded_file($_FILES['photo']['tmp_name'], $path);
            // $user->photo = $path;
            return $path;
        }
        // if($user->save())
            // return response()->json(['message' => 'Uploaded Successfully']);
    }
}
