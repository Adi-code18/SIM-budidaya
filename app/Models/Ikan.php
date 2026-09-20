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
    ];

    protected $casts = [
        'fcr_min'         => 'float',
        'fcr_max'         => 'float',
        'bulan_panen_min' => 'float',
        'bulan_panen_max' => 'float',
    ];

    protected $appends = [
        'avg_ekor_per_kg',
    ];

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
