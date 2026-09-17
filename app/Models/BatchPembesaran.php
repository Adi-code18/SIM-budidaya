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
     * Hitung FCR aktual kolam (Kumulatif saat berjalan, Komersial saat selesai).
     */
    public function calculateActualFcr(): float
    {
        // Total akumulasi pakan pada kolam ini sejak tanggal tebar
        $queryPakan = ManajemenPakan::where('id_kolam', $this->id_kolam);
        if ($this->tgl_tebar) {
            $queryPakan->whereDate('tgl_log', '>=', $this->tgl_tebar);
        }
        $totalPakan = (float) ($queryPakan->sum('kg_pelet') + $queryPakan->sum('kg_daun'));

        // Biomassa awal tebar
        $biomassaAwal = 0;
        if ($this->batchPembibitan && $this->batchPembibitan->total_bobot_kg > 0) {
            $biomassaAwal = (float) $this->batchPembibitan->total_bobot_kg;
        }

        // Pertambahan biomassa
        if (strtolower($this->status_siklus ?? '') === 'selesai' && $this->jumlah_panen_kg > 0) {
            $pertambahanBiomassa = max(0.1, (float) $this->jumlah_panen_kg - $biomassaAwal);
        } else {
            $currentBiomassa = (float) ($this->biomassa_est > 0 ? $this->biomassa_est : 0);
            $pertambahanBiomassa = max(0.1, $currentBiomassa - $biomassaAwal);
            if ($pertambahanBiomassa <= 0 && $currentBiomassa > 0) {
                $pertambahanBiomassa = $currentBiomassa;
            }
        }

        if ($totalPakan > 0 && $pertambahanBiomassa > 0) {
            return round($totalPakan / $pertambahanBiomassa, 2);
        }

        // Jika belum ada log pakan atau pakan 0, kembalikan nilai fcr yang tersimpan atau target ikan
        if ($this->fcr && (float)$this->fcr > 0) {
            return (float) $this->fcr;
        }

        $ikan = $this->ikan_ref;
        return $ikan ? (float)$ikan->fcr_min : 1.10;
    }
}
