<?php

namespace Database\Factories;

use App\Models\Lietotajs;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Lietotajs>
 */
class LietotajsFactory extends Factory
{
    protected $model = Lietotajs::class;

    protected static ?string $password;

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
}
