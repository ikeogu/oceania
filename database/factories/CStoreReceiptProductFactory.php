<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CStoreReceiptProduct;

class CStoreReceiptProductFactory extends Factory
{
    protected $model = CStoreReceiptProduct::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
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
