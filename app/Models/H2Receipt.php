<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// use H2ReceiptDetails;

class H2Receipt extends Model
{
    use HasFactory;

    protected $table ="h2receipt";


    // public function user()
    // {
        // return $this->belongsTo('App\Models\User');

    //   return $this->belongsTo('App\Models\User',"staff_user_id","id");

    //   return $this->belongsTo('App\Models\User',"staff_user_id","id");

    // }
    public function staff_user()
    {
        // return $this->belongsTo(User::class, 'staff_user_id', 'owner_key');

      return $this->belongsTo('App\Models\User',"staff_user_id","id");
    }

    public function h2receiptdetails(){
        return $this->hasOne(H2ReceiptDetails::class, 'receipt_id','id');
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
