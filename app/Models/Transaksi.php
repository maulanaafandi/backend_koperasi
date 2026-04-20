<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    public $timestamps = true;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = null;
    protected $fillable = [
        'id_nasabah',
        'kode_transaksi',
        'jenis_transaksi',
        'saldo_sebelum',
        'saldo_sesudah',
        'status_transaksi',
        'waktu_dibuat',
        'dibuat_oleh',
        'saldo',
        'waktu_transaksi_sukses',
        ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
        'waktu_dibuat' => 'datetime',
        'waktu_transaksi_sukses' => 'datetime',
    ];

    public function nasabah()
    {
       return $this->belongsTo(Nasabah::class, 'id_nasabah');
    }
}
