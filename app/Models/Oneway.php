<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class oneway extends Model
{
    use HasFactory;
    protected $table ="oneway";
       public function findastatus($id , $owner_user_id){


           $owner_id = DB::table('lic_locationkey')->join("company", "company.id", "lic_locationkey.company_id")->pluck('owner_user_id')->first();
          
             $get_mer_id = DB::table('merchantLink')
             ->where(['initiator_user_id' => $owner_id , 'responder_user_id' => $owner_user_id])->pluck('id')->first();
             if (empty($get_mer_id)){
               $get_mer_id = DB::table('merchantLink')
             ->where(['responder_user_id' => $owner_id])->pluck('id')->first();
              return Db::table('merchantlinkrelation')->where('company_id' , $id)->where('ptype' , "supplier")->where('merchantlink_id' , $get_mer_id)->pluck('status')->first();
             }else{
               $status =  Db::table('merchantlinkrelation')->where('company_id' , $id)->where('ptype' , "supplier")->where('merchantlink_id' , $get_mer_id)->pluck('status')->first();
               if (empty($status)){
                  return "pending";
               }else{
                  return $status;
               }

             }
        
          

        
    }
    public function getStatusval($id){

       return DB::table('onewayrelation')->where('oneway_id' , $id)->where('ptype' , "dealer")->orderby('id' , 'desc')->pluck('status')->first();
    }

}
