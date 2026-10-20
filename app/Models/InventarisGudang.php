<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarisGudang extends Model
{
    use HasFactory;

    protected $table = 'inventaris_gudang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_barang', // saprotan, peralatan
        'tipe_saprotan',   // obat_kimia, vitamin_suplemen, desinfektan_kapur, pengepakan_panen, lainnya
        'satuan',          // kg, liter, sak, botol, unit, set, roll, pcs
        'stok_tersisa',
        'min_stok',
        'harga_satuan_terakhir',
        'kondisi_alat',    // normal, perlu_servis, rusak
        'lokasi_penyimpanan',
        'catatan'
    ];

    protected $casts = [
        'stok_tersisa' => 'float',
        'min_stok' => 'float',
        'harga_satuan_terakhir' => 'float',
    ];

    /**
     * Relasi ke transaksi mutasi gudang
     */
    public function logTransaksi()
    {
        return $this->hasMany(LogTransaksiGudang::class, 'id_barang', 'id_barang');
    }

    /**
     * Hitung total nilai stok aset barang ini
     */
    public function getNilaiTotalStokAttribute()
    {
        return (float) ($this->stok_tersisa * $this->harga_satuan_terakhir);
    }

    /**
     * Indikator status stok (Aman / Waspada / Kritis)
     */
    public function getStatusStokAttribute()
    {
        if ($this->stok_tersisa <= 0) {
            return 'habis';
        }
        if ($this->stok_tersisa <= $this->min_stok) {
            return 'kritis';
        }
        if ($this->stok_tersisa <= ($this->min_stok * 1.5)) {
            return 'waspada';
        }
        return 'aman';
    }
}
