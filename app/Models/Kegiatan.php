<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'tahun',
    ];

    // Satu kegiatan bisa dipakai banyak peta
    public function peta()
    {
        return $this->hasMany(Peta::class, 'kegiatan_id');
    }

    // Satu kegiatan bisa ada di banyak transaksi
    public function transaksiPeta()
    {
        return $this->hasMany(TransaksiPeta::class, 'kegiatan_id');
    }
}