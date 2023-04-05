<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CStoreItemDetails;

class CStoreItemDetailsFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CStoreItemDetails::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            "amount" => 4717,
            "rounding" => 0,
            "price" => 300,
            "sst" => 283,
            "discount" => 0,
        ];
    }
}
