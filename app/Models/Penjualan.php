<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = ['client_id','tanggal','total','status'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function suratJalan()
    {
        return $this->hasOne(SuratJalan::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnPenjualan::class, 'penjualan_id');
    }
}