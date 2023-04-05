<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SyncSalesController extends Controller
{
    
    public static function curlRequest($url, $params = null)
    {
        $ch = curl_init($url);

        /*
        $headers = array(
            'Authorization: Bearer '.$_SESSION['token'],
        );
        */

        if ($params !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, "data=$params");
            curl_setopt($ch, CURLOPT_POST, 1);
        }
                        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }
}
