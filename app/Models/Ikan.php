<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ikan extends Model
{
    use HasFactory;

    protected $table = 'ikan';
    protected $primaryKey = 'id_ikan';

    protected $fillable = [
        'nama_ikan',
        'durasi_penetasan',
        'durasi_pembibitan',
        'fcr_min',
        'fcr_max',
        'bulan_panen_min',
        'bulan_panen_max',
        'target_konsumsi',
        'jenis_pakan_didukung',
        'harga_jual_kg',
        'foto',
    ];

    protected $casts = [
        'fcr_min'         => 'float',
        'fcr_max'         => 'float',
        'bulan_panen_min' => 'float',
        'bulan_panen_max' => 'float',
        'harga_jual_kg'   => 'float',
    ];

    protected $appends = [
        'avg_ekor_per_kg',
        'foto_url',
    ];

    /**
     * URL Foto Ikan (dari storage upload atau fallback foto autentik per spesies)
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto)) {
            $version = $this->updated_at ? $this->updated_at->timestamp : time();
            return asset('storage/' . $this->foto) . '?v=' . $version;
        }

        $namaLower = strtolower($this->nama_ikan ?? '');
        if (str_contains($namaLower, 'nila')) {
            return 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80';
        }
        if (str_contains($namaLower, 'lele')) {
            return 'https://images.unsplash.com/photo-1524704654690-b56c05c78a00?auto=format&fit=crop&w=1200&q=80';
        }
        if (str_contains($namaLower, 'gurame') || str_contains($namaLower, 'gurami')) {
            return 'https://images.unsplash.com/photo-1522069169874-c58ec4b76be5?auto=format&fit=crop&w=1200&q=80';
        }
        if (str_contains($namaLower, 'patin')) {
            return 'https://images.unsplash.com/photo-1534043464124-3be32fe000c9?auto=format&fit=crop&w=1200&q=80';
        }
        if (str_contains($namaLower, 'bawal')) {
            return 'https://images.unsplash.com/photo-1524704654690-b56c05c78a00?auto=format&fit=crop&w=1200&q=80';
        }
        if (str_contains($namaLower, 'mas')) {
            return 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80';
        }
        if (str_contains($namaLower, 'tawes') || str_contains($namaLower, 'nilem')) {
            return 'https://images.unsplash.com/photo-1522069169874-c58ec4b76be5?auto=format&fit=crop&w=1200&q=80';
        }

        return 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80';
    }

    /**
     * Hitung rata-rata ekor per kg dari target_konsumsi (misal: "8–10 ekor / kg" -> 9.0)
     */
    public function getAvgEkorPerKgAttribute(): float
    {
        if (!$this->target_konsumsi) {
            return 4.0;
        }
        preg_match_all('/\d+(?:[\.,]\d+)?/', $this->target_konsumsi, $matches);
        if (!empty($matches[0])) {
            $nums = array_map(fn($v) => (float) str_replace(',', '.', $v), $matches[0]);
            if (count($nums) > 0) {
                return round(array_sum($nums) / count($nums), 2);
            }
        }
        return 4.0;
    }

    /**
     * Kalkulasi Target Panen (Kg) otomatis berdasarkan Unit Economics SOP Perikanan:
     * Target Panen (Kg) = (Jumlah Bibit Tebar * Survival Rate %) / (Target Ekor per Kg)
     */
    public static function calculateTargetPanen(float $jumlahBibit, float $survivalRate = 85.0, float $ekorPerKg = 4.0): float
    {
        if ($ekorPerKg <= 0) {
            $ekorPerKg = 4.0;
        }
        $srMultiplier = $survivalRate > 1 ? ($survivalRate / 100.0) : $survivalRate;
        $ekorHidup = $jumlahBibit * $srMultiplier;
        return round($ekorHidup / $ekorPerKg, 1);
    }

    public function batchPembibitan()
    {
        return $this->hasMany(BatchPembibitan::class, 'id_ikan', 'id_ikan');
    }
}
