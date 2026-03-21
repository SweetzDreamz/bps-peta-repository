<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = [
        'kode_prop',
        'kode_kab',
        'kode_kec',
        'kode_desa',
        'kode_blok',
        'nama_prop',
        'nama_kab',
        'nama_kec',
        'nama_desa',
    ];

    // Satu wilayah bisa punya banyak SLS
    public function sls()
    {
        return $this->hasMany(Sls::class, 'wilayah_id');
    }

    // Satu wilayah bisa punya banyak peta
    public function peta()
    {
        return $this->hasMany(Peta::class, 'wilayah_id');
    }

    // Helper untuk menampilkan nama lengkap wilayah
    public function getNamaLengkapAttribute(): string
    {
        if ($this->kode_blok) {
            return "{$this->nama_desa} - Blok {$this->kode_blok}";
        }
        return "{$this->nama_kec} - {$this->nama_desa}";
    }
}