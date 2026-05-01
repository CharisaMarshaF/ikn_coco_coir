<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Rekening extends Model
{
    use SoftDeletes; 

    protected $table = 'rekening';

    protected $fillable = ['nama', 'jenis', 'saldo'];

    public function kasHarian()
    {
        return $this->hasMany(KasHarian::class);
    }
}