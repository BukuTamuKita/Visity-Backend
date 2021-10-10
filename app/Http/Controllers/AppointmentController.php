<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Guest;
use App\Models\Host;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class AppointmentController extends Controller
{
    public $order_table = 'appointments';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (Gate::allows('admin')) {
                $appointment = Appointment::query();
            } else if (Gate::allows('host')) {
                $host = Host::firstWhere('user_id', $this->user->id);
                if ($host == null)
                    throw new ModelNotFoundException('Host with User ID ' . $this->user->id . ' Not Found', 0);

                $appointment = Appointment::where('host_id', $host->id);
            }
                $appointment = $appointment->when([$this->order_table, $this->orderBy], Closure::fromCallable([$this, 'queryOrderBy']))
                ->when($this->limit, Closure::fromCallable([$this, 'queryLimit']));
         
            return AppointmentResource::collection($appointment);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => $e->getMessage(),
            ]);
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
        $current_date = Carbon::now()->format('Y-m-d');
        $current_time = Carbon::now()->format('H:i');
        //
        $this->validate($request, [
            'host' => 'required|string',
            'guest' => 'required|string',
            'purpose' => 'required|string|max:255',
        ]);
     
        $appointment = Appointment::create([
            'host_id'=> $request->host,
            'guest_id' => $request->guest,
            'purpose' => $request->purpose,
            'status' => 'waiting',
            'date' => $current_date,
            'time' => $current_time,
        ]);

        return response()->json($appointment, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {
            return new AppointmentResource(Appointment::findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'Appointment ' . $id . ' not found.'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'status' => 'required|in:accepted,declined',
            'notes' => 'string|max:255|nullable',
        ]);

        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update($request->all());

            return new AppointmentResource($appointment);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'Appointment ' . $id . ' not found.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {
            Appointment::findOrFail($id)->delete();

            return response()->json([], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'code' => 404,
                'message' => 'Not Found',
                'description' => 'Appointment ' . $id . ' not found.'
            ], 404);
        }
    }

    public function export_excel(){
        $current_date = Carbon::now()->format('d-m-Y_H:i');

        return Excel::download(new AppointmentExport, 'Data-Appointment-'.$current_date.'.xlsx');
    }
}
