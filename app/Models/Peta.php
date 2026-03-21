<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peta extends Model
{
    protected $table = 'peta';

    protected $fillable = [
        'jenis_peta',
        'wilayah_id',
        'sls_id',
        'kegiatan_id',
        'tahun',
        'versi',
        'path_file',
        'user_id',
    ];

    // Peta dimiliki oleh satu wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    // Peta bisa dimiliki oleh satu SLS (nullable untuk WA dan WB)
    public function sls()
    {
        return $this->belongsTo(Sls::class, 'sls_id');
    }

    // Peta terkait satu kegiatan
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    // Peta diupload oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Satu peta bisa punya banyak transaksi
    public function transaksiPeta()
    {
        return $this->hasMany(TransaksiPeta::class, 'peta_id');
    }

    // Helper untuk mendapatkan URL file peta
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->path_file);
    }
}