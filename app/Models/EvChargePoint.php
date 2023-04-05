<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvChargePoint extends Model
{
    //
    protected $table = "ev_chargepoint";

    protected $fillable = ['systemid', 'name', 'ocpp_protocol', 'vendor', 'chargepoint_serial_no',
'firmware_version', 'meter_type', 'meter_serial_no', 'address', 'latitude', 'longitude'];

}
