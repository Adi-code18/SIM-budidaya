<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchPembesaran extends Model
{
    use HasFactory;

    protected $table = 'batch_pembesaran';
    protected $primaryKey = 'id_pembesaran';

    protected $fillable = [
        'id_kolam',
        'id_user',
        'id_batch_pembibitan',
        'asal_bibit',
        'biaya_beli_bibit',
        'tgl_tebar',
        'est_tgl_panen',
        'biomassa_est',
        'fcr',
        'target_panen_kg',
        'jumlah_panen_kg',
        'jenis_ikan',
        'status_siklus',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kolam()
    {
        return $this->belongsTo(Kolam::class, 'id_kolam', 'id_kolam');
    }

    public function batchPembibitan()
    {
        return $this->belongsTo(BatchPembibitan::class, 'id_batch_pembibitan', 'id_batch');
    }

    /**
     * Relasi/Referensi master ikan acuan untuk patokan FCR dan siklus panen.
     */
    public function getIkanRefAttribute()
    {
        if ($this->batchPembibitan && $this->batchPembibitan->ikan) {
            return $this->batchPembibitan->ikan;
        }

        if ($this->jenis_ikan) {
            $clean = trim(preg_replace('/^(ikan\s+)/i', '', $this->jenis_ikan));
            return Ikan::where('nama_ikan', 'LIKE', '%' . $clean . '%')->first();
        }

        return null;
    }

    /**
     * Hitung FCR Kumulatif (Running FCR) saat siklus berjalan hari ke-N.
     * Formula SOP: Akumulasi Pakan Hari 1 s.d. N (kg) / (Estimasi Biomassa Hari ke-N - Biomassa Awal Tebar)
     */
    public function calculateCumulativeFcr(): ?float
    {
        $totalPakan = $this->getTotalPakanKg();
        if ($totalPakan <= 0) {
            return null; // Belum ada konsumsi pakan yang dicatat
        }

        $biomassaAwal = $this->getBiomassaAwalKg();
        $currentBiomassa = (float) ($this->biomassa_est > 0 ? $this->biomassa_est : 0);
        $pertambahanBiomassa = max(0.1, $currentBiomassa - $biomassaAwal);

        return round($totalPakan / $pertambahanBiomassa, 2);
    }

    /**
     * Hitung FCR Komersial (Digunakan saat panen selesai).
     * Formula SOP: Total Pakan yang Diberikan (kg) / (Biomassa Panen Riil - Biomassa Awal Tebar)
     */
    public function calculateCommercialFcr(): ?float
    {
        $totalPakan = $this->getTotalPakanKg();
        if ($totalPakan <= 0) {
            return null;
        }

        $biomassaAwal = $this->getBiomassaAwalKg();
        $biomassaPanen = (float) ($this->jumlah_panen_kg > 0 ? $this->jumlah_panen_kg : $this->biomassa_est);
        $pertambahanBiomassa = max(0.1, $biomassaPanen - $biomassaAwal);

        return round($totalPakan / $pertambahanBiomassa, 2);
    }

    /**
     * Hitung FCR Biologis (Termasuk estimasi ikan mati / mortalitas).
     * Formula SOP: Total Pakan yang Diberikan (kg) / ((Biomassa Panen + Estimasi Biomassa Mati) - Biomassa Awal Tebar)
     */
    public function calculateBiologicalFcr(float $mortalitasKg = 0): ?float
    {
        $totalPakan = $this->getTotalPakanKg();
        if ($totalPakan <= 0) {
            return null;
        }

        $biomassaAwal = $this->getBiomassaAwalKg();
        $biomassaPanen = (float) ($this->jumlah_panen_kg > 0 ? $this->jumlah_panen_kg : $this->biomassa_est);
        $totalBiomassaHasil = ($biomassaPanen + $mortalitasKg) - $biomassaAwal;
        $pertambahanBiomassa = max(0.1, $totalBiomassaHasil);

        return round($totalPakan / $pertambahanBiomassa, 2);
    }

    /**
     * Hitung FCR Periodik / Sampling.
     * Formula SOP: Total Pakan Selama Periode Ini (kg) / (Biomassa Akhir Periode - Biomassa Awal Periode)
     */
    public function calculatePeriodicFcr(float $pakanPeriodeKg, float $biomassaAwalPeriodeKg, float $biomassaAkhirPeriodeKg): ?float
    {
        $deltaBiomassa = max(0.1, $biomassaAkhirPeriodeKg - $biomassaAwalPeriodeKg);
        if ($pakanPeriodeKg > 0 && $deltaBiomassa > 0) {
            return round($pakanPeriodeKg / $deltaBiomassa, 2);
        }
        return null;
    }

    /**
     * Hitung FCR aktual kolam (Kompatibilitas).
     */
    public function calculateActualFcr(): float
    {
        if (strtolower($this->status_siklus ?? '') === 'selesai') {
            return $this->calculateCommercialFcr() ?? 0.00;
        }
        return $this->calculateCumulativeFcr() ?? 0.00;
    }

    /**
     * Ambil total akumulasi pakan yang diberikan ke kolam ini sejak tanggal tebar (kg).
     */
    public function getTotalPakanKg(): float
    {
        $queryPakan = ManajemenPakan::where('id_kolam', $this->id_kolam);
        if ($this->tgl_tebar) {
            $queryPakan->whereDate('tgl_log', '>=', $this->tgl_tebar);
        }
        return (float) ($queryPakan->sum('kg_pelet') + $queryPakan->sum('kg_daun'));
    }

    /**
     * Ambil biomassa awal saat pertama kali tebar (kg).
     */
    public function getBiomassaAwalKg(): float
    {
        if ($this->batchPembibitan && $this->batchPembibitan->total_bobot_kg > 0) {
            return (float) $this->batchPembibitan->total_bobot_kg;
        }
        return (float) max(0, round(($this->biomassa_est ?: 50) * 0.1, 1));
    }

    /**
     * Hitung Analisis Finansial Laba / Rugi (Keuntungan & Kerugian) Kolam.
     * Menggunakan pendekatan HPP Riil: Jika data pakan berjalan belum lengkap,
     * sistem secara otomatis memproyeksikan kebutuhan pakan berdasarkan standar FCR komoditas.
     */
    public function calculateFinancials(): array
    {
        $actualPakanKg = $this->getTotalPakanKg();
        $isSelesai = strtolower($this->status_siklus ?? '') === 'selesai';

        // 1. Dapatkan Standar FCR dan Harga Jual Pasar Komoditas
        $jenis = strtolower($this->jenis_ikan ?? '');
        $ikanRef = Ikan::where('nama_ikan', 'like', '%' . $this->jenis_ikan . '%')->first();

        $targetFcr = 1.25;
        if ($ikanRef && $ikanRef->fcr_min > 0 && $ikanRef->fcr_max > 0) {
            $targetFcr = ($ikanRef->fcr_min + $ikanRef->fcr_max) / 2;
        } elseif (str_contains($jenis, 'patin')) {
            $targetFcr = 1.20;
        } elseif (str_contains($jenis, 'nila')) {
            $targetFcr = 1.25;
        } elseif (str_contains($jenis, 'lele')) {
            $targetFcr = 1.10;
        } elseif (str_contains($jenis, 'gurame') || str_contains($jenis, 'gurami')) {
            $targetFcr = 1.45;
        }

        // Harga Jual Pasar Acuan (Rp/Kg)
        $hargaJualPerKg = 24000;
        if (str_contains($jenis, 'lele')) {
            $hargaJualPerKg = 23000;
        } elseif (str_contains($jenis, 'nila')) {
            $hargaJualPerKg = 32000;
        } elseif (str_contains($jenis, 'patin')) {
            $hargaJualPerKg = 26000;
        } elseif (str_contains($jenis, 'gurame') || str_contains($jenis, 'gurami')) {
            $hargaJualPerKg = 48000;
        } elseif (str_contains($jenis, 'mas')) {
            $hargaJualPerKg = 34000;
        } elseif (str_contains($jenis, 'bawal')) {
            $hargaJualPerKg = 28000;
        }

        // 2. Pendapatan (Omset Estimasi atau Aktual Penjualan)
        $biomassaJual = $isSelesai 
            ? (float) ($this->jumlah_panen_kg > 0 ? $this->jumlah_panen_kg : $this->biomassa_est)
            : (float) ($this->biomassa_est > 0 ? $this->biomassa_est : 0);

        $pendapatanEstimasi = round($biomassaJual * $hargaJualPerKg);

        // 3. Proyeksi Kebutuhan Pakan Riil (Mengatasi Gap Input Data Petugas)
        $biomassaAwal = $this->getBiomassaAwalKg();
        $deltaBiomassa = max(0, $biomassaJual - $biomassaAwal);
        $kebutuhanPakanSOP = round($deltaBiomassa * $targetFcr, 1);

        // Gunakan pakan SOP jika data input riil masih belum terisi/sangat minim
        $totalPakanPerhitungan = max($actualPakanKg, $kebutuhanPakanSOP);
        if ($isSelesai && $actualPakanKg > ($deltaBiomassa * 0.7)) {
            $totalPakanPerhitungan = $actualPakanKg;
        }

        $avgHargaPakan = 12000;
        $peletPrice = StokPakan::where('nama_pakan', 'like', '%Pelet%')
            ->where('satuan', 'kg')
            ->where('harga_per_satuan', '>', 0)
            ->avg('harga_per_satuan');

        if ($peletPrice && $peletPrice > 0) {
            $avgHargaPakan = round((float) $peletPrice);
        }
        $biayaPakan = round($totalPakanPerhitungan * $avgHargaPakan);

        // 4. Biaya Bibit
        $biayaBibit = (float) ($this->biaya_beli_bibit ?? 0);
        if ($biayaBibit <= 0) {
            if ($this->batchPembibitan && (float)$this->batchPembibitan->total_bobot_kg > 0) {
                $biayaBibit = round((float) $this->batchPembibitan->total_bobot_kg * 40000);
            } else {
                // Estimasi modal benih awal (sekitar 6% - 8% dari omset panen)
                $biayaBibit = round(max(300000, $pendapatanEstimasi * 0.07));
            }
        }

        // 5. Biaya Operasional Kolam Tambahan dari Buku Kas
        $biayaOperasional = 0;
        if ($this->id_kolam && $this->tgl_tebar) {
            $biayaOperasional = (float) Keuangan::where('id_kolam', $this->id_kolam)
                ->where('tipe_transaksi', 'pengeluaran')
                ->where('ref_id', 'not like', 'BELI-BIBIT%')
                ->where('kategori', 'not like', '%pakan%')
                ->whereDate('tanggal_transaksi', '>=', $this->tgl_tebar)
                ->sum('nominal');
        }

        // Total Modal / Pengeluaran Kolam (HPP)
        $totalBiayaKolam = $biayaPakan + $biayaBibit + $biayaOperasional;

        // 6. Laba / Rugi Bersih & Margin
        $labaRugi = $pendapatanEstimasi - $totalBiayaKolam;
        $marginPercent = $pendapatanEstimasi > 0 ? round(($labaRugi / $pendapatanEstimasi) * 100, 1) : 0;

        $statusFinansial = 'profit';
        $statusLabel = 'Untung (Profit)';
        $statusBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';

        if ($labaRugi < 0) {
            $statusFinansial = 'loss';
            $statusLabel = 'Defisit / Rugi';
            $statusBadge = 'bg-rose-100 text-rose-800 border-rose-200';
        } elseif ($labaRugi == 0) {
            $statusFinansial = 'bep';
            $statusLabel = 'Impas (BEP)';
            $statusBadge = 'bg-amber-100 text-amber-800 border-amber-200';
        }

        return [
            'total_pakan_kg'      => $totalPakanPerhitungan,
            'actual_pakan_kg'     => $actualPakanKg,
            'avg_harga_pakan'     => $avgHargaPakan,
            'biaya_pakan'         => $biayaPakan,
            'biaya_bibit'         => $biayaBibit,
            'biaya_operasional'   => $biayaOperasional,
            'total_biaya_kolam'   => $totalBiayaKolam,
            'harga_jual_per_kg'   => $hargaJualPerKg,
            'biomassa_jual_kg'    => $biomassaJual,
            'pendapatan_estimasi' => $pendapatanEstimasi,
            'laba_rugi'           => $labaRugi,
            'margin_percent'      => $marginPercent,
            'status_finansial'    => $statusFinansial,
            'status_label'        => $statusLabel,
            'status_badge'        => $statusBadge,
        ];
    }
}
