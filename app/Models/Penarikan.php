<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    protected $table = 'penarikan';
    public $timestamps = true;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_transaksi',
        'jumlah_penarikan',
        'dibuat_oleh'
    ];

    protected $casts = [
        'jumlah_penarikan' => 'decimal:2',
        'waktu_dibuat' => 'datetime',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }
}
