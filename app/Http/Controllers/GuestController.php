<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public $order_table = 'guests';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Guest::when([$this->order_table, $this->orderBy], \Closure::fromCallable([$this, 'queryOrderBy']))
        ->when($this->limit, \Closure::fromCallable([$this, 'queryLimit']));

        return GuestResource::collection($user);
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
            'name' => 'required|string',
            'nik' => 'required|string',
            'email' => 'nullable|string',
            'address' => 'required|string|max:255',
        ]);
        $guest = Guest::where('nik',$request->nik)->first();
        if(Guest::where('nik',$request->nik)->exists()){
           $guest->update($request->all());
        } else {
            $guest = Guest::create($request->all());
        }
        return response()->json($guest, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {
            return new GuestResource(Guest::findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'Guest ' . $id . ' not found.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function edit(Guest $guest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Guest $guest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Guest $guest)
    {
        //
    }

    public function getAppointments(int $id){
        try {
            $appointment = Guest::findOrFail($id)
                ->appointments()
                ->when(['appointments', $this->orderBy], Closure::fromCallable([$this, 'queryOrderBy']))
                ->when($this->limit, Closure::fromCallable([$this, 'queryLimit']));

            return AppointmentResource::collection($appointment);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'Guest ' . $id . ' not found.'
            ], 404);
        }
    }
}
