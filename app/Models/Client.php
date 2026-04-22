<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    protected $fillable = ['nama','telp','alamat','catatan'];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}
