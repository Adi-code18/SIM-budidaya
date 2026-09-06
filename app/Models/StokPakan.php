<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokPakan extends Model
{
    use HasFactory;

    protected $table = 'stok_pakan';
    protected $primaryKey = 'id_stok_pakan';

    protected $fillable = [
        'nama_pakan',
        'kategori_peruntukan',
        'satuan',
        'stok_tersisa',
        'batas_minimum',
        'harga_per_satuan',
        'keterangan',
    ];

    public function pembelian()
    {
        return $this->hasMany(PembelianPakan::class, 'id_stok_pakan', 'id_stok_pakan');
    }

    public function logs()
    {
        return $this->hasMany(ManajemenPakan::class, 'id_stok_pakan', 'id_stok_pakan');
    }
}
