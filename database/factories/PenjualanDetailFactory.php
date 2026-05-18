<?php

namespace Database\Factories;

use App\Models\PenjualanDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PenjualanDetail>
 */
class PenjualanDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 20);
        $harga = fake()->numberBetween(50000, 200000);
        
        return [
            'produk_id' => \App\Models\Produk::factory(), // Mengasumsikan sudah ada ProdukFactory
            'qty' => $qty,
            'harga' => $harga,
            'subtotal' => $qty * $harga,
        ];
    }
}
