<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Order;
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
        $categories = Category::get();
        return [
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'promotion_price' => $this->faker->randomFloat(2, 10, 1000),

            'features' => json_encode($this->faker->randomElements(['feature1', 'feature2', 'feature3', 'feature4', 'feature5', 'feature6', 'feature7', 'feature8'], 5)),

            'status' => $this->faker->boolean,
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'category_id' => $this->faker->randomElement($categories)->id

        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $product->orders()->attach(Order::factory()->create());
        });
    }
}
