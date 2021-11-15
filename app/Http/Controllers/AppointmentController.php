<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\GuestResource;
use App\Models\Appointment;
use App\Models\Guest;
use App\Models\Host;
use Carbon\Carbon;
use Closure;
use CurlHandle;
use App\Mail\VisityEmail;
use CURLFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Concerns\ToArray;
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
        $current_date = Carbon::now()->format('d M Y');
        $current_time = Carbon::now()->format('H:i');
        //
        $this->validate($request, [
            'host' => 'required|numeric',
            'guest' => 'required|numeric',
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
            'status' => 'required|in:accepted,declined,canceled',
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

    public function scan_ktp(Request $request){
        $rules = array(
            'image' => 'required|mimes:jpeg,jpg,png|max:2048' 
            );
        $validator = validator()->make($request->only('image'), $rules);

        if($validator->fails()){
            return response()->json([
                'code' => 400,
                'message' => 'Bad Request',
                'description' => 'Scan Failed!, Image not Found or Wrong format'
            ], 400);
              
        } else {
            $image = $request->file('image');
            $url = 'https://campus.sindika.co.id/index.php/aiktpextractor/extract.json';
            $data = ['image' => new CURLFile($image)]; 
            $headers = ['Secret: 7CB1912A835DAEBCE58BDEA4EC899',
        'Content-Type: multipart/form-data'];
            $ch = curl_init();
        
            // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                   
            curl_setopt($ch,CURLOPT_URL,$url);
            // curl_setopt($ch, CURLOPT_UPLOAD, true);
            // curl_setopt($ch, CURLOPT_INFILE, $data);
            curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            // curl_setopt($ch, CURLOPT_SSL_ENABLE_NPN, false);
            // curl_setopt($ch, CURLOPT_SSL_ENABLE_ALPN, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($ch, CURLSSLOPT_NO_REVOKE,true);
            // curl_setopt($ch, CURLOPT_PROXY_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            
          
            $resp = curl_exec($ch);

            if(curl_error($ch)){
                return response()->json([
                    'error' => curl_error($ch)
                ]);
            } else {
                $decoded = json_decode($resp);
                return response()->json([$decoded],200);
            }
            // return json_decode($resp);
            curl_close($ch);
        }
        
    }

    public function sendEmail(int $id){
        $guest = Appointment::findOrFail($id)->guest();
        $guestId = $guest->pluck('id');
        $getGuest = Guest::where('id',$guestId)->select("name","email")->first();
        $guestName = $getGuest->name;
        $guestEmail = $getGuest->email;
        $appointment = Appointment::findOrFail($id);
        $status = $appointment->status;
        $notes = $appointment->notes;

        $details = [
            'title' => 'Hello '. $guestName. ',',
            'heading' => '***PLEASE DO NOT REPLY TO THIS EMAIL***',
            'body'=> 'Hasil keputusan appointment dengan Host: '. $status,
            'note' => 'Dengan keterangan tambahan:  '. $notes,
        ];

        if($guestEmail==null){
            return response()->json([
                'code' => 200,
                'message' => 'OK',
                'description' => 'Success',
            ], 200); 
        } else{
            Mail::to($guestEmail)->send(new VisityEmail($details));

            return response()->json([
                'code' => 200,
                'message' => 'OK',
                'description' => 'Email Konfirmasi telah dikirim halo halo',
            ], 200); 
        }
     
    }
}
