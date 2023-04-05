<?php

namespace App\Http\Controllers;

use App\Classes\SystemID;
use App\Models\EvChargePoint;
use App\Models\EvConnector;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ThunderController extends Controller
{
    public function startTransaction(Request $request)
    {
        $client = new Client();
        $response = $client->request('POST', 'http://'.env('THUNDER_IPADDR').'/steve/remote_start_transaction', [
            'form_params' => [
                'name' => 'start'
            ]
        ]);
			Log::debug('actionStatus: '. (String) $response->getBody());

        return $response;
    }

    public function storeChargePoint(Request $request)
    {
        
		Log::debug('actionStatus: '. (String) $request);
        $request['name'] = $request['systemid'];
        $request['systemid'] = new SystemID('chargepoint');
        $charge_point = EvChargePoint::query()->create($request->all());
        $connector_data = [
            'chargepoint_id' => $charge_point->id,
            'connector_no' => $request['connector_no'],
            'connector_pk' => $request['connector_pk']

        ];
        EvConnector::query()->create($connector_data);
        return $request;
    }
}
