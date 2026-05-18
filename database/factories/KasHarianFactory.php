<?php

namespace Database\Factories;

use App\Models\KasHarian;
use App\Models\KategoriKas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KasHarian>
 */
class KasHarianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array {
    return [
        'rekening_id' => 1, // Sesuaikan atau gunakan Rekening::factory()
        'kategori_kas_id' => 1,
        'tanggal' => fake()->dateTimeBetween('-1 month', 'now'),
        'jenis' => fake()->randomElement(['masuk', 'keluar']),
        'kategori' => 'operasional',
        'total_nominal' => 0, // Akan diupdate dari total detail
        'keterangan' => fake()->sentence(),
    ];
}
}
