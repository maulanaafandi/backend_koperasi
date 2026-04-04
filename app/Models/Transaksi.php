<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'id_anggota',
        'kode_transaksi', 
        'jumlah',
        'sumber'
        ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

    public function simpanan()
    {
        return $this->hasOne(Simpanan::class, 'id_transaksi');
    }
}
