<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $fillable = ['penjualan_id','nomor','tanggal','total','status_bayar'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
