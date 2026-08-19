<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraDistributor extends Model
{
    use HasFactory;

    protected $table = 'mitra_distributor';
    protected $primaryKey = 'id_mitra';

    protected $fillable = [
        'id_user',
        'nama_mitra',
        'tipe_mitra',
        'alamat',
        'longitude',
        'latitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function transaksiDistribusi()
    {
        return $this->hasMany(TransaksiDistribusi::class, 'id_mitra', 'id_mitra');
    }
}
