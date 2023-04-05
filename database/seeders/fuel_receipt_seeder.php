<?php

namespace Database\Seeders;

use App\Models\AuthReceipt;
use App\Models\EodDetails;
use App\Models\FuelItemDetails;
use App\Models\FuelReceipt;
use App\Models\FuelReceiptdetails;
use App\Models\fuelReceiptProduct;
use App\Models\ReceiptFilled;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class fuel_receipt_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $number_of_days = 30; // number of days to be considered
        $number_of_insert_records = 100; // number of receipts per day
        $seconds_increase = 54;
        $auth_id = 1550000000457;


        for ($i=0; $i < $number_of_days; $i++) {
            if ($i > 0) {
                $timeToUse = strtotime(date('Y-m-d 06:00:00',
					strtotime('2021-04-01 +'.$i.' days')));

            } else {
                $timeToUse = strtotime(date('2021-04-01 06:00:00'));
            }

            for ($x=0; $x < $receipts_per_day; $x++) { 
                $this_time_now = date('Y-m-d H:i:s', $timeToUse);
        
                FuelReceipt::factory()->create([
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                $insertReceiptID = DB::getPdo()->lastInsertId();
        
                fuelReceiptProduct::factory()->create([
                    'receipt_id' => $insertReceiptID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                FuelReceiptdetails::factory()->create([
                    'receipt_id' => $insertReceiptID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                $insertProductID = DB::getPdo()->lastInsertId();

                FuelItemDetails::factory()->create([
                    'receiptproduct_id' => $insertProductID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                EodDetails::factory()->create([
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                ReceiptFilled::factory()->create([
                    'auth_systemid' => $auth_id,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                AuthReceipt::factory()->create([
                    'auth_systemid' => $auth_id,
                    'receipt_id' => $insertReceiptID,
                    "created_at" => $this_time_now,
                    'updated_at' => $this_time_now,
                ]);

                $timeToUse = $timeToUse + $seconds_increase;
                $auth_id = $auth_id + $x + 1;
            }
        }
    }
}
