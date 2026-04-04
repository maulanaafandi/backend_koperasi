<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';
    protected $fillable = [
        'id_anggota',
        'id_transaksi', 
        'id_tenor', 
        'jumlah_pinjaman', 
        'sisa_pinjaman', 
        'status'
    ];

    public function anggota() {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

    public function tenor() {
        return $this->belongsTo(Tenor::class, 'id_tenor');
    }

    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
