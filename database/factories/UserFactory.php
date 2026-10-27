<?php

namespace Database\Factories;

use App\Models\Lietotajs;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Lietotajs>
 */
class UserFactory extends Factory
{
    protected $model = Lietotajs::class;

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
            'vards' => fake()->firstName(),
            'uzvards' => fake()->lastName(),
            'name' => fake()->name(),
            'e_pasts' => fake()->unique()->safeEmail(),
            'email' => fake()->unique()->safeEmail(),
            'parole' => static::$password ??= Hash::make('password'),
            'password' => static::$password ??= Hash::make('password'),
            'loma' => 0,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this;
    }
}
