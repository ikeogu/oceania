<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SelfServiceController extends Controller
{
    public function SelfServiceList (){

         return view('self_service.selfservice_receiptlist');
    }
}
