<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'no_tlp',
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

    public function pengajuanLibur()
    {
        return $this->hasMany(PengajuanLibur::class, 'id_user', 'id_user');
    }

    public function kolam()
    {
        return $this->hasMany(Kolam::class, 'id_user', 'id_user');
    }

    public function manajemenPakan()
    {
        return $this->hasMany(ManajemenPakan::class, 'id_user', 'id_user');
    }

    public function batchPembibitan()
    {
        return $this->hasMany(BatchPembibitan::class, 'id_user', 'id_user');
    }

    public function batchPembesaran()
    {
        return $this->hasMany(BatchPembesaran::class, 'id_user', 'id_user');
    }

    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_user', 'id_user');
    }

    public function mitraDistributor()
    {
        return $this->hasMany(MitraDistributor::class, 'id_user', 'id_user');
    }

    public function transaksiDistribusi()
    {
        return $this->hasMany(TransaksiDistribusi::class, 'id_user', 'id_user');
    }
}
