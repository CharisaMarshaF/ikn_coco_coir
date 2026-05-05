<?php

namespace App\Models;

use App\Models\ReturnDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPenjualan extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit karena nama model tidak jamak (optional)
    protected $table = 'returns';

    protected $fillable = [
        'penjualan_id',
        'nomor_return',
        'tanggal',
        'total_refund',
        'is_resend' // Tambahkan ini agar bisa diupdate
    ];

    // Relasi ke Header Penjualan (Asal Return)
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    // Relasi ke Detail Item yang Direturn
    public function detail()
    {
        return $this->hasMany(ReturnDetail::class, 'return_id');
    }
}