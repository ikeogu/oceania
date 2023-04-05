<?php

namespace Database\Factories;

use App\Models\fuelReceiptProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuelReceiptProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = fuelReceiptProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "receipt_id" => 1,
            "product_id" => 1,
            "name" => "Diesel",
            "quantity" => 1.5,
            "price" => 0.2 * 100,
            "discount_pct" => 0,
            "discount" => 0,
            "created_at" => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
