<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    public $timestamps = false; 
    protected $table = 'jenis_simpanan';
    protected $fillable = [
        'jenis_simpanan',
        'saldo_minimum',
        'updated_at',
        'updated_by'
        ];


    public function simpanan()
    {
        return $this->hasMany(Simpanan::class, 'id_jenis_simpanan');
    }
}
