<?php

namespace Database\Factories;

use App\Models\EodDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

class EodDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EodDetails::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "eod_id" => 1,
            "startdate" => date('Y-m-d'),
            "total_amount" => 5000,
            "rounding" => 0,
            "sales" => 4717,
            "sst" => 283,
            "discount" => 0,
            "cash" => 0,
            "cash_change" => 0,
            "creditcard" => 0,
            "wallet" => 5000,
        ];
    }
}
