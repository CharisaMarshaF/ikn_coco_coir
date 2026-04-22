<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $fillable = ['supplier_id','tanggal','total','status_pembayaran'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }
    public function getDataPembayaranAttribute()
    {
        // Mencari baris di transaksi keuangan yang keterangannya mengandung ID pembelian ini
        $search = "#PB-" . str_pad($this->id, 5, '0', STR_PAD_LEFT);
        
        return \App\Models\TransaksiKeuangan::with('rekening')
            ->where('keterangan', 'LIKE', "%{$search}%")
            ->first();
    }
}