<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sls extends Model
{
    protected $table = 'sls';

    protected $fillable = [
        'wilayah_id',
        'kode_sls',
        'kode_sub_sls',
        'nama_sls',
        'nama_ketua',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // SLS dimiliki oleh satu wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    // Satu SLS bisa punya banyak peta
    public function peta()
    {
        return $this->hasMany(Peta::class, 'sls_id');
    }
}