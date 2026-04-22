<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKeuangan extends Model
{
        protected $casts = [
    'tanggal' => 'date',
];
    protected $table = 'transaksi_keuangan';
    protected $fillable = ['rekening_id','tanggal','jenis','sumber','nominal','keterangan'];

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }
}
