<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CicilanPinjaman extends Model
{
    protected $table = 'cicilan_pinjaman';
    public $timestamps = false;

    protected $fillable = [
        'id_pinjaman',
        'id_transaksi',
        'nomor_angsuran',
        'tanggal_jatuh_tempo',
        'total_tagihan',
        'tagihan_pokok',
        'bunga',
        'denda',
        'waktu_dibayar',
        'status_angsuran',
        'dibayar_oleh',
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'tagihan_pokok' => 'decimal:2',
        'bunga' => 'decimal:2',
        'denda' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'waktu_dibayar' => 'datetime',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'id_pinjaman');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
