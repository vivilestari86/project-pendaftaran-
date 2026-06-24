<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone_number' => fake()->numerify('08##########'),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['admin', 'user']),
            'status' => fake()->randomElement(['Active', 'Inactive']),
            'last_active_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'terms_agreed' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'status' => 'Active',
            'last_active_at' => now(),
        ]);
    }

    public function activeUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
            'status' => 'Active',
            'last_active_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
