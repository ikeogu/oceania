<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Evreceipt extends Model
{
    protected $guarded = ['id'];
    protected $table = "evreceipt";
    public function staff_user()
    {
        return $this->belongsTo('App\Models\User',"staff_user_id","id");
    }

    public static function getCurrentLoginOut()
    {
        $loginOut = DB::table('loginout')->
            where("user_id", Auth::user()->id)->
            where("logout", null)->
            first();
        return $loginOut;
    }

    /**
     * @return HasMany
     */
    public function receiptDetails() {
        return $this->hasMany('App\Models\Evreceiptdetails', 'evreceipt_id');
    }
}
