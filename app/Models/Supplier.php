<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['nama','telp','alamat','keterangan'];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }
}
