<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = [
        'id_subsls', 'nama_sls', 'nama_ketua', 'jenis',
        'kode_prop', 'kode_kab', 'kode_kec', 'kode_des',
        'kode_sls', 'kode_subsls', 'klas',
        'nama_prop', 'nama_kab', 'nama_kec', 'nama_des',
        'kk', 'btt', 'bttk', 'bku', 'bbtt_nonusaha',
        'usaha', 'muatan', 'status', 'asal',
        'wilayah_asal_ids', 'induk_id', 'perlu_edit',
    ];

    protected $casts = [
        'wilayah_asal_ids' => 'array',
        'perlu_edit'       => 'boolean',
    ];

    // Relasi ke wilayah induk (hasil gabung/pecah)
    public function induk()
    {
        return $this->belongsTo(Wilayah::class, 'induk_id');
    }

    // Wilayah turunan (hasil gabung/pecah dari wilayah ini)
    public function turunan()
    {
        return $this->hasMany(Wilayah::class, 'induk_id');
    }

    // Wilayah asal dari ids yang tersimpan
    public function wilayahAsal()
    {
        if (empty($this->wilayah_asal_ids)) return collect();
        return Wilayah::whereIn('id', $this->wilayah_asal_ids)->get();
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function peta()
    {
        return $this->hasMany(Peta::class, 'wilayah_id');
    }

    public function sls()
    {
        return $this->hasMany(Sls::class, 'wilayah_id');
    }
}