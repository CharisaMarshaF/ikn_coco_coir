<?php
namespace App\Models;

use App\Models\HasilProduksiDetail;
use Illuminate\Database\Eloquent\Model;

class HasilProduksi extends Model
{
    protected $table = 'hasil_produksi';
    protected $fillable = ['tanggal', 'kode_produksi', 'keterangan', 'user_id'];

    public function details() {
        return $this->hasMany(HasilProduksiDetail::class);
    }

        public function user() {
            return $this->belongsTo(User::class);
        }
        
}