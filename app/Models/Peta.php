<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peta extends Model
{
    protected $table = 'peta';

    protected $fillable = [
        'jenis_peta',
        'wilayah_id',
        'kegiatan_id',
        'tahun',
        'path_file',
        'user_id',
    ];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksiPeta()
    {
        return $this->hasMany(TransaksiPeta::class, 'peta_id');
    }
}