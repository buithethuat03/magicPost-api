<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userID' => Str::uuid(),
            'fullname' => $this->faker->name(),
            'phoneNumber' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'userType' => $this->faker->randomElement(['-1', '0', '1', '2', '3', '4']),
            'belongsTo' => null, // or $this->faker->mediumInteger() for a random value
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        // return $this->state(fn (array $attributes) => [
        //     'email_verified_at' => null,
        // ]);
    }
}
