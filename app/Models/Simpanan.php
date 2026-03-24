<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $table = 'simpanan';
    protected $fillable = ['id_anggota', 'id_jenis_simpanan', 'id_transaksi', 'jumlah_simpanan'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota');
    }

    public function jenis_simpanan()
    {
        return $this->belongsTo(JenisSimpanan::class, 'id_jenis_simpanan');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
