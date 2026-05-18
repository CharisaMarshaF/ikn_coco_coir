<?php

namespace Database\Factories;

use App\Models\KasDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KasDetail>
 */
class KasDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array {
    $jumlah = fake()->numberBetween(1, 10);
    $harga = fake()->numberBetween(10000, 500000);
    return [
        'nama_item' => fake()->words(3, true),
        'jumlah' => $jumlah,
        'harga' => $harga,
        'subtotal' => $jumlah * $harga,
    ];
}
}
