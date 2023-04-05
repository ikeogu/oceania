<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvConnector extends Model
{
    //
    protected $table = "ev_connector";

    protected $fillable = ['connector_pk', 'connector_no', 'chargepoint_id', 'ocpp_protocol'];

}
