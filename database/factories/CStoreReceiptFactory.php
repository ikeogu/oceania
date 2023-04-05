<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CStoreReceipt;
use Illuminate\Support\Str;

class CStoreReceiptFactory extends Factory
{

    protected $model = CStoreReceipt::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            "systemid" => "106000005".random_int(1000000000, 9999999999),
            "payment_type" => "cash",
            "cash_received" => 0,
            "cash_change" => 0,

            "service_tax" => 50.00,
            "terminal_id" => 5,
            "mode" => "inclusive",

            "staff_user_id" => 2,
            "company_id" => 1,
            "company_name" => $this->faker->name(),
            "gst_vat_sst" => "25234d4-34",
            "business_reg_no" => "534234-N",
            "receipt_logo" => "barney_logo.jpeg",

			"currency" => 'MYR',

            "status" => "active",
            "remark" => "NULL",            

            "transacted" => "pos",
        ];
    }
}
