<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'rekening';
    protected $fillable = ['nama','jenis','saldo_awal','saldo_saat_ini'];

    public function transaksi()
    {
        return $this->hasMany(TransaksiKeuangan::class);
    }
}
