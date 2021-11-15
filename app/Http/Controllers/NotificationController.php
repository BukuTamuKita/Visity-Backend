<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function send(Request $request)
    {
        $user = User::where('name',$request->name)->select('device_token')->first();
        $device = $user->device_token;
        if($device){
            return $this->sendNotification(array($device), array(
            "title" => $request->gname, 
            "body" => $request->body
            ));
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'OK',
                'description' => 'Success',
            ], 200); 
        }
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function sendNotification($device_token, $message)
    {
        $SERVER_API_KEY = 'ServerKeyFCM';
  
        // payload data, it will vary according to requirement
        $data = [
            "registration_ids" => $device_token, // for single device id
            "notification" => $message
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
      
        curl_close($ch);
      
        return $response;
    }
}
