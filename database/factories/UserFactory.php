<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ar_SA');
        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(), 
            'phone' => $faker->phoneNumber(),
            'phone_2' => $faker->phoneNumber(),
            'password' => $faker->password(),
            'approved' => 1,  
            'identity_num' => $faker->unique()->numerify('##########'),
            'user_type'=> 'staff',
        ];
    } 
}
