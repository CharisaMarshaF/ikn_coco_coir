<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
public function run(): void
{
    // Membuat 50 data KasHarian, yang masing-masing punya 3 detail
\App\Models\Penjualan::factory()->count(50)->create();
}
}
