<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kolam extends Model
{
    use HasFactory;

    protected $table = 'kolam';
    protected $primaryKey = 'id_kolam';

    protected $fillable = [
        'id_user',
        'nama_kolam',
        'tipe_kolam',
        'kapasitas',
        'status',
        'kesehatan_ph_air',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function manajemenPakan()
    {
        return $this->hasMany(ManajemenPakan::class, 'id_kolam', 'id_kolam');
    }

    public function batchPembibitan()
    {
        return $this->hasMany(BatchPembibitan::class, 'id_kolam', 'id_kolam');
    }

    public function batchPembesaran()
    {
        return $this->hasMany(BatchPembesaran::class, 'id_kolam', 'id_kolam');
    }

    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_kolam', 'id_kolam');
    }
}
