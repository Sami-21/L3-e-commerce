<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'price' => $this->faker->price(10, 1000),
            'colors' => $this->faker->randomElements([
                'red', 'black', 'white', 'green', 'blue', 'rgb', 'yellow',
                'orange'
            ], 3),
            'features' => $this->faker->randomElements(['feature1', 'feature2', 'feature3', 'feature4', 'feature5', 'feature6', 'feature7', 'feature8'], 5),
            'capacity' => $this->faker->randomElements([
                '128GB', '256GB', '512GB', '1TB', '2TB', '10TB'
            ], 3),
            'status' => $this->faker->boolean,
            'rating' => $this->faker->randomFloat(2, 0, 0),
            'category_id' => Category::factory()->create()->id,
        ];
    }
}
