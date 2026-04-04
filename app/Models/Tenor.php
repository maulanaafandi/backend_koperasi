<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenor extends Model
{
    protected $table = 'tenor';
    protected $fillable = [
        'tenor',
        'tipe',
        'bunga'
        ];
    public $timestamps = false;
    /**
     * Relasi ke model Pinjaman.
     * Satu tenor bisa digunakan oleh banyak data pinjaman.
     */
    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'id_tenor');
    }
}