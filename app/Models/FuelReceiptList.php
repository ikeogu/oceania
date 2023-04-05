<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelReceiptList extends Model
{

    protected $table = 'fuel_receiptlist';


    public function user(){

        return $this->belongsTo(User::class,'refund_staff_user_id','id');
    }
}
