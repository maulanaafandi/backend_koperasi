<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenor extends Model
{
    public $timestamps = false;
    protected $table = 'tenor';
    protected $fillable = [
        'tenor',
        'tipe',
        'bunga',
        'created_by',
        'updated_at',
        'updated_by'
        ];
    /**
     * Relasi ke model Pinjaman.
     * Satu tenor bisa digunakan oleh banyak data pinjaman.
     */
    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'id_tenor');
    }
}