<?php

namespace Database\Factories;

use App\Models\Penjualan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penjualan>
 */
class PenjualanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => 1, // Mengasumsikan sudah ada ClientFactory
            'tanggal' => fake()->dateTimeBetween('-2 months', 'now'),
            'total' => 0, // Akan diupdate setelah detail dibuat
            'status' => fake()->randomElement(['pending', 'lunas', 'dikirim']),
        ];
    }

    public function configure()
{
    return $this->afterCreating(function (\App\Models\Penjualan $penjualan) {
        // Buat 2 sampai 5 item detail untuk setiap penjualan
        $details = \App\Models\PenjualanDetail::factory()
            ->count(rand(2, 5))
            ->create(['penjualan_id' => $penjualan->id]);

        // Update total nominal di header penjualan
        $penjualan->update([
            'total' => $details->sum('subtotal')
        ]);
    });
}
}
