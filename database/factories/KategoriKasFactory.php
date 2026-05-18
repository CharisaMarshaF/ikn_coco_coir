<?php

namespace Database\Factories;

use App\Models\KategoriKas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriKas>
 */
class KategoriKasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array {
    return [
        'nama' => fake()->randomElement(['Investasi', 'Produksi', 'Rumah Tangga']),
    ];
}
}
