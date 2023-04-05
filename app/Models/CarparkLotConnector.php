<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarparkLotConnector extends Model
{
    //
    protected $table = "carparklotconnector";

    protected $fillable = ['carparklot_id', 'connector_id'];

}
