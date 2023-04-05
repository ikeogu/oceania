<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OEWReceiptList extends Model
{

    protected $table = 'oew_receiptlist';


    public function user(){

        return $this->belongsTo(User::class,'refund_staff_user_id','id');
    }
}
