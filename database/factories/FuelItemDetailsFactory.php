<?php

namespace Database\Factories;

use App\Models\FuelItemDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuelItemDetailsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FuelItemDetails::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "amount" => 4717,
            "rounding" => 0,
            "price" => 300,
            "sst" => 283,
            "discount" => 0,
        ];
    }
}
