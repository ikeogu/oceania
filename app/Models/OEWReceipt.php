<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OEWReceipt extends Model
{
    use HasFactory;
    protected $table = "oew_receipt";

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'void_user_id', 'id');
    }

    public static function getCurrentLoginOut()
    {
        $loginOut = DB::table('loginout')->
            where("user_id", Auth::user()->id)->
            where("logout", null)->
            first();
        return $loginOut;
    }

}
