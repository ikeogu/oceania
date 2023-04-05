<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PrdOpenitem;

class PrdOpenitemFactory extends Factory
{
    protected $model = PrdOpenitem::class;
    /**
     * Define the model's default state.
     *
     * 
     */
    public function definition()
    {
        return [
            //
            "qty" => 3,
            "price" => 3000,
            "cost" => 0,
            "status" => 'active',              
        ];
    }
}
