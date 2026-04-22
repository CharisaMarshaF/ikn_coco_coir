<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalan';
    protected $fillable = ['penjualan_id','nomor','tanggal','status_kirim'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
