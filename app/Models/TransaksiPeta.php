<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPeta extends Model
{
    protected $table = 'transaksi_peta';

    protected $fillable = [
        'peta_id',
        'user_id',
        'kegiatan_id',
        'tanggal',
        'status',
        'file_kembali',
        'waktu_kembali',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'waktu_kembali' => 'datetime',
    ];

    public function peta()
    {
        return $this->belongsTo(Peta::class, 'peta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function isDipinjam(): bool
    {
        return $this->status === 'dipinjam';
    }

    public function isDikembalikan(): bool
    {
        return $this->status === 'dikembalikan';
    }
}