<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CStoreReceiptDetails;

class CStoreReceiptDetailsFactory extends Factory
{

    protected $model = CStoreReceiptDetails::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            "total" => 5000,
            "rounding" => 0,
            "item_amount" => 4717,
            "sst" => 283,
            "discount" => 0,
            "cash_received" => 0,
            "change" => 0,
            "creditcard" => 0,
            "wallet" => 5000,           
        ];
    }
}
