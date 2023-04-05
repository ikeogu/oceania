<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PrdInventory;

class PrdInventoryFactory extends Factory
{
    protected $model = PrdInventory::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            "qty" => 3,
            "price" => 3000,
            "pending" => 0,
            "cost" => 0,
            "cogs" => 0,
            "loyalty" => 50,            
        ];
    }
}
