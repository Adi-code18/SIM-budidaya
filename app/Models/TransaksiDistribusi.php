<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDistribusi extends Model
{
    use HasFactory;

    protected $table = 'transaksi_distribusi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_user',
        'id_mitra',
        'tanggal_order',
        'Total_kg',
        'harga_total',
        'status_order',
        'Jenis_order',
        'Bukti_sampai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function mitra()
    {
        return $this->belongsTo(MitraDistributor::class, 'id_mitra', 'id_mitra');
    }
}
