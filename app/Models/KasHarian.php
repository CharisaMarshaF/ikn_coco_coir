<?php

namespace App\Models;

use App\Models\KasDetail;
use Illuminate\Database\Eloquent\Model;

class KasHarian extends Model
{
    protected $table = 'kas_harian';

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected $fillable = [
        'rekening_id',
        'tanggal',
        'jenis',
        'kategori',
        'total_nominal',
        'keterangan'
    ];

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    // Relasi ke tabel detail
    public function details()
    {
        return $this->hasMany(KasDetail::class, 'kas_harian_id');
    }
}