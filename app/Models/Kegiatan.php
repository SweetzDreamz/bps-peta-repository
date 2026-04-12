<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function peta()
    {
        return $this->hasMany(Peta::class, 'kegiatan_id');
    }

    public function transaksiPeta()
    {
        return $this->hasMany(TransaksiPeta::class, 'kegiatan_id');
    }
}