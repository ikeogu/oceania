<?php

namespace Database\Factories;

use App\Models\FuelReceiptdetails;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuelReceiptDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FuelReceiptdetails::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "total" => 5000,
            "rounding" => 0,
            "item_amount" => 4717,
            "sst" => 283,
            "discount" => 0,
            "cash_received" => 0,
            "change" => 0,
            "creditcard" => 0,
            "wallet" => 5000,
            "creditac" => 0,
        ];
    }
}
