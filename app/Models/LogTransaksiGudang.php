<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTransaksiGudang extends Model
{
    use HasFactory;

    protected $table = 'log_transaksi_gudang';
    protected $primaryKey = 'id_transaksi_gudang';

    protected $fillable = [
        'no_referensi',
        'jenis_mutasi', // masuk_pembelian, keluar_pemakaian_kolam, penyesuaian_rusak
        'id_barang',
        'jumlah',
        'harga_satuan',
        'total_biaya',
        'id_kolam',
        'id_user',
        'tanggal_mutasi',
        'keterangan'
    ];

    protected $casts = [
        'jumlah' => 'float',
        'harga_satuan' => 'float',
        'total_biaya' => 'float',
        'tanggal_mutasi' => 'date'
    ];

    /**
     * Relasi ke barang gudang
     */
    public function barang()
    {
        return $this->belongsTo(InventarisGudang::class, 'id_barang', 'id_barang');
    }

    /**
     * Relasi ke kolam penerima barang (jika ada pemakaian kolam)
     */
    public function kolam()
    {
        return $this->belongsTo(Kolam::class, 'id_kolam', 'id_kolam');
    }

    /**
     * Relasi ke user / petugas pelaksana
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
