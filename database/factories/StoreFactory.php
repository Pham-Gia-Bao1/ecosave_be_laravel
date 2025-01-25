<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        return [
            'store_name' => $this->faker->company,
            'avatar' => $this->faker->imageUrl(200, 200, 'business', true, 'Faker'),
            'store_type' => $this->faker->randomElement(['Grocery', 'Electronics', 'Clothing']),
            'opening_hours' => '9:00 AM - 9:00 PM',
            'status' => $this->faker->randomElement(['active', 'inactive', 'closed']),
            'contact_email' => $this->faker->safeEmail,
            'contact_phone' => $this->faker->numerify('+###########'),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(),
        ];
    }
}
