<?php

use App\Models\AppLog;
use Carbon\Carbon;


use Illuminate\Support\Facades\Http;



    function HttpRequest($url, $params=[] ){
              
        $res=[];
        $token= env('API_ACCESS_TOKEN');
        $response = Http::withToken($token)->get($url, $params);
        if ($response->successful()) {
            $data = $response->json();
            $res['data']=$data; 
           
        } else {
            // Handle error response
            $error = $response->body();
            // Do something with the error
        }

        return $res ; 
    }

    function savelog($message="",$filename="", $payload=[] ){
        $app_log = new AppLog;
        $app_log->message= $message;
        $app_log->filename= $filename;
        $app_log->payload= json_encode($payload, true );
        $app_log->save();
        return true  ; 
    }


    function formatDate($dateString)
    {
        // Parse the date string using Carbon
        $date = Carbon::parse($dateString);

        // Format the date to 'Y-m-d' (e.g., 2024-07-30)
        $formattedDate = $date->format('Y-m-d H:i:s');
        return $formattedDate; 

    }





    if (!function_exists('formatCurrency')) {
        function formatCurrency($number) {
            $number = round($number); // Ensure the number is an integer

            // Convert the number to string to manipulate it
            $number = (string) $number;

            // Remove any non-numeric characters
            $number = preg_replace('/\D/', '', $number);

            // Split the number into parts
            $length = strlen($number);
            if ($length <= 3) {
                return $number;
            } elseif ($length <= 5) {
                return substr($number, 0, $length - 3) . ',' . substr($number, -3);
            } else {
                $last3Digits = substr($number, -3);
                $remainingDigits = substr($number, 0, -3);
                $formattedNumber = '';
                while (strlen($remainingDigits) > 2) {
                    $formattedNumber = ',' . substr($remainingDigits, -2) . $formattedNumber;
                    $remainingDigits = substr($remainingDigits, 0, -2);
                }
                return $remainingDigits . $formattedNumber . ',' . $last3Digits;
            }
        }
    }






?>