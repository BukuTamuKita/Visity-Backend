<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use App\Http\Resources\HostResource;
use App\Models\Host;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HostController extends Controller
{
    public $order_table = 'hosts';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $hosts = Host::all()->toArray();
        // $hosts = Host::find(2)->user->email;
        // $hosts = Host::when([$this->order_table, $this->orderBy], \Closure::fromCallable([$this, 'queryOrderBy']))
        // ->when($this->limit, \Closure::fromCallable([$this, 'queryLimit']));

        // return HostResource::collection($hosts);
        if (Gate::allows('admin')) {
            $host = Host::when([$this->order_table, $this->orderBy], Closure::fromCallable([$this, 'queryOrderBy']))
                ->when($this->limit, Closure::fromCallable([$this, 'queryLimit']));

            return HostResource::collection($host);
            
        } elseif (Gate::allows('host')) {
            $host = Host::firstWhere('user_id', $this->user->id);

            return new HostResource($host);
        }
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
        // $this->validate($request, [
        //     'name' => 'required',
        //     'nip' => 'required',
        //     'position' => 'required',
        //     'user_id' => 'required'
        // ]);
        // // Host::create([
        // //     'name' => request('name'),
        // //     'nip' => request('nip'),
        // //     'position' => request('position'),
        // //     'user_id' => auth()->id()
        // // ]);

        // $host = Host::create($request->all());
        // return response()->json($host, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Host  $host
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {
            return new HostResource(Host::findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'Host ' . $id . ' not found.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Host  $host
     * @return \Illuminate\Http\Response
     */
    public function edit(Host $host)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Host  $host
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Host $host)
    {
        //
        // $this->validate($request, [
        //     'host' => 'required|string',
        //     'guest' => 'required|string',
        //     'purpose' => 'required|string|max:255',
        // ]);
        // $host = Host::where('name',$request->host)->firstOrFail();
        // $guest = Guest::where('name',$request->guest)->firstOrFail();

        // // $hostId = $hostId->id;
        // // $guestId = $guestId->id;
        // $appointment = Appointment::create([
        //     'host_id'=> $host->id,
        //     'guest_id' => $guest->id,
        //     'purpose' => $request->purpose,
        //     'status' => 'waiting',
        //     'date' => $current_date,
        //     'time' => $current_time,
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Host  $host
     * @return \Illuminate\Http\Response
     */
    public function destroy(Host $host)
    {
        //
    }

    public function getAppointments(int $id){
        try {
            $appointment = Host::findOrFail($id)
                ->appointments()
                ->when(['appointments', $this->orderBy], Closure::fromCallable([$this, 'queryOrderBy']))
                ->when($this->limit, Closure::fromCallable([$this, 'queryLimit']));

            return AppointmentResource::collection($appointment);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'Host ' . $id . ' not found.'
            ], 404);
        }
    }
}
