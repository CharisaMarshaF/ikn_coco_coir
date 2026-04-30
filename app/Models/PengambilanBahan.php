<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanBahan extends Model
{
    use HasFactory;

    protected $table = 'pengambilan_bahan';

    protected $fillable = [
        'tanggal',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke detail pengambilan (Satu ke Banyak).
     */
    public function details()
    {
        return $this->hasMany(PengambilanBahanDetail::class, 'pengambilan_id');
    }
}