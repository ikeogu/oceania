<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Date;

class ManualRecordController extends Controller
{


    public function getManualRecord()
    {
        $location = DB::table('location')->first();
        $user = auth()->user();
        $time = date('dMy H:i:s');
        return view('manual_record.manual_record', ['time' => $time, 'location' => $location, 'user' => $user]);
    }

    public function getManualRecordList()
    {
        return view('manual_record.manual_record_list');
    }
}
