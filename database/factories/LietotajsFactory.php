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

    /**
     * Parole, ko lieto visi rūpnīcas lietotāji (testos: 'password').
     */
    protected static ?string $parole;

    public function definition(): array
    {
        return [
            'vards' => fake()->firstName(),
            'uzvards' => fake()->lastName(),
            // e_pasts kolonna ir max 30 rakstzīmes
            'e_pasts' => 'lietotajs' . fake()->unique()->numberBetween(1, 999999) . '@piemers.lv',
            'parole' => static::$parole ??= Hash::make('password'),
            'loma' => 0,
        ];
    }

    public function administrators(): static
    {
        return $this->state(fn () => ['loma' => 1]);
    }
}
