<?php

namespace App\Helpers;

use App\Models\StokBahan;
use App\Models\StokProduk;

class StokHelper
{
    public static function updateStokBahan($bahanId, $jumlah)
    {
        $stok = StokBahan::firstOrCreate(
            ['bahan_id' => $bahanId],
            ['jumlah' => 0]
        );

        // Pastikan mengambil properti 'jumlah', bukan objek modelnya
        $currentStok = (float) $stok->jumlah; 
        $stokBaru = $currentStok + $jumlah;

        if ($stokBaru < 0) {
            return false;
        }

        $stok->update(['jumlah' => $stokBaru]);
        return true;
    }

    public static function updateStokProduk($produkId, $jumlah)
    {
        $stok = StokProduk::firstOrCreate(
            ['produk_id' => $produkId],
            ['jumlah' => 0]
        );

        $currentStok = (float) $stok->jumlah;
        $stokBaru = $currentStok + $jumlah;

        if ($stokBaru < 0) {
            return false;
        }

        $stok->update(['jumlah' => $stokBaru]);
        return true;
    }
}