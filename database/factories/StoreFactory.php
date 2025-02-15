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
            'avatar' => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwe2nD_ynzqIF89lhrxCMtUd9EEOKqK3JdRg&s",
            'logo' => "https://chothuenhapho.vn/wp-content/uploads/2022/05/sieu-thi-winmart-can-thue-mat-bang-o-hcm.jpg",
            'store_type' => $this->faker->randomElement(['Grocery', 'Electronics', 'Clothing']),
            'opening_hours' => '9:00 AM - 9:00 PM',
            'status' => $this->faker->randomElement(['active', 'inactive', 'closed']),
            'contact_email' => $this->faker->safeEmail,
            'contact_phone' => $this->faker->numerify('+###########'),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'soft_description' => $this->faker->paragraph,
            'description' => $this->faker->paragraph,
            'address' => $this->faker->address,
            'user_id' => User::factory(),
        ];
    }
}
