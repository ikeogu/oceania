<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CStoreReceipt;
use App\Models\CStoreReceiptProduct;
use App\Models\CStoreReceiptDetails;
use App\Models\CStoreItemDetails;
use App\Models\PrdInventory;
use App\Models\PrdOpenitem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $number_of_days = 30; // number of days to be considered
        $number_of_insert_records = 1600; // number of receipts per day
        $seconds_increase = 54;
        $auth_id = 1020000000274;


        for ($i=0; $i < $number_of_days; $i++) {
            if ($i > 0) {
                $timeToUse = strtotime(date('Y-m-d 06:00:00',
					strtotime('2022-08-01 +'.$i.' days')));

            } else {
                $timeToUse = strtotime(date('2022-08-01 06:00:00'));
            }

            for ($x=0; $x < $number_of_insert_records; $x++) { 
                $this_time_now = date('Y-m-d H:i:s', $timeToUse);
        
                CStoreReceipt::factory()->create([
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                $insertReceiptID = DB::getPdo()->lastInsertId();
                $productIDs = rand(1,44);
        
                CStoreReceiptProduct::factory()->create([
                    'receipt_id' => $insertReceiptID,
                    'product_id' => $productIDs,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                CStoreReceiptDetails::factory()->create([
                    'receipt_id' => $insertReceiptID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                $insertProductID = DB::getPdo()->lastInsertId();

                CStoreItemDetails::factory()->create([
                    'receiptproduct_id' => $insertProductID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);     
                
               // $ProductID = DB::getPdo()->lastInsertId();
                $ProductID = rand(1,44);

                PrdInventory::factory()->create([
                    'product_id' => $ProductID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

             /*  PrdOpenitem::factory()->create([
                    'product_id' => $ProductID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);   */     

                $timeToUse = $timeToUse + $seconds_increase;
                $auth_id = $auth_id + $x + 1;
            }
        }

    }
}
