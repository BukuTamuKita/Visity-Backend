<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // public $order_table = 'users';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $users = User::all();
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
            // $data = new UserResource(User::findOrFail($id));
            // return new UserResource(User::findOrFail($id));
            return new UserResource(User::where('role','!=','admin')->findOrFail($id));
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
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function getTokens(int $id)
    {
        try {
            $tokens = User::findOrFail($id)->tokens;

            return response()->json($tokens);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'User ' . $id . ' not found.'
            ], 404);
        }
    }

    public function createToken(Request $request, int $id)
    {
        // TODO: Fix sesuai field Token
        $this->validate($request, [
            'type' => 'required',
        ]);

        try {
            $token = Token::create([
                'user_id' => User::findOrFail($id),
                // TODO: Fix generate token
                'token' => Str::random(10),
                'type' => $request->type,
            ]);

            return response()->json($token, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'User ' . $id . ' not found.'
            ], 404);
        }
    }
}
