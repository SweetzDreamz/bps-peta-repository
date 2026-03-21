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
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Transaksi terkait satu peta
    public function peta()
    {
        return $this->belongsTo(Peta::class, 'peta_id');
    }

    // Transaksi dilakukan oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Transaksi terkait satu kegiatan
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    // Helper cek status
    public function isDipinjam(): bool
    {
        return $this->status === 'dipinjam';
    }

    public function isDikembalikan(): bool
    {
        return $this->status === 'dikembalikan';
    }
}