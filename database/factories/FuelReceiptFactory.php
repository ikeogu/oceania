<?php

namespace Database\Factories;

use App\Models\FuelReceipt;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuelReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FuelReceipt::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "systemid" => "1580000030000000009",
            "payment_type" => "cash",
            "cash_received" => 0,
            "cash_change" => 0,

            "service_tax" => 50.00,
            "terminal_id" => 3,
            "mode" => "inclusive",

            "staff_user_id" => 2,
            "company_id" => 1,
            "company_name" => $this->faker->name(),
            "gst_vat_sst" => "25234d4-34",
            "business_reg_no" => "534234-N",
            "receipt_logo" => "barney_logo.png",

			"currency" => 'MYR',

            "status" => "active",
            "remark" => "NULL",

            "pump_id" => "4",
            "pump_no" => "4",

            "transacted" => "pos",
        ];
    }
}
