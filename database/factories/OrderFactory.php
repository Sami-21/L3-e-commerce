<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $client =  Client::factory()->create();
        return [
            'client_id' => $client->id,
            'firstname' => $client->user->firstname,
            'lastname' => $client->user->lastname,
            'phone' => $this->faker->phoneNumber,
            'wilaya' => $this->faker->word,
            'city'
            => $this->faker->word,
            'address'
            => $this->faker->address,
            'status'
            => OrderStatusEnum::Pending,
            'notes'
            => $this->faker->text,
            'total'
            => $this->faker->randomFloat(2, 500, 999999),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $order->products()->attach(Product::factory()->count(3)->create());
        });
    }
}