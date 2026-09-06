<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianPakan extends Model
{
    use HasFactory;

    protected $table = 'pembelian_pakan';
    protected $primaryKey = 'id_pembelian';

    protected $fillable = [
        'id_stok_pakan',
        'id_mitra',
        'id_user',
        'tgl_beli',
        'jumlah',
        'harga_satuan',
        'total_biaya',
        'no_nota',
        'catatan',
    ];

    public function stokPakan()
    {
        return $this->belongsTo(StokPakan::class, 'id_stok_pakan', 'id_stok_pakan');
    }

    public function mitra()
    {
        return $this->belongsTo(MitraDistributor::class, 'id_mitra', 'id_mitra');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
