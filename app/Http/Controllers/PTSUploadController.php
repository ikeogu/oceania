<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\Request;

class PTSUploadController extends Controller
{
    public function pts_upload(Request $request) {
		Log::info('***** pts_upload() *****');	
		Log::info(json_encode($request->all()));
	}
}
