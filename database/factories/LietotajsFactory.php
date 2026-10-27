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

    protected static ?string $parole;

    public function definition(): array
    {
        return [
            'vards' => fake()->firstName(),
            'uzvards' => fake()->lastName(),
            'name' => fake()->name(),
            'e_pasts' => 'lietotajs' . fake()->unique()->numberBetween(1, 999999) . '@piemers.lv',
            'email' => fake()->unique()->safeEmail(),
            'parole' => static::$parole ??= Hash::make('password'),
            'password' => static::$parole ??= Hash::make('password'),
            'loma' => 0,
        ];
    }

    public function administrators(): static
    {
        return $this->state(fn () => ['loma' => 1]);
    }
}
