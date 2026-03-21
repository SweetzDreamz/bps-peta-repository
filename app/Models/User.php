<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Satu user bisa mengupload banyak peta
    public function peta()
    {
        return $this->hasMany(Peta::class, 'user_id');
    }

    // Satu user bisa melakukan banyak transaksi
    public function transaksiPeta()
    {
        return $this->hasMany(TransaksiPeta::class, 'user_id');
    }

    // Helper cek role
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }
}