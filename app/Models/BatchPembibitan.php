<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchPembibitan extends Model
{
    use HasFactory;

    protected $table = 'batch_pembibitan';
    protected $primaryKey = 'id_batch';

    protected $fillable = [
        'id_kolam',
        'id_user',
        'id_ikan',
        'tgl_pemijahan',
        'est_prcs_pembibitaan',
        'jumlah_bibitAwal',
        'jumlah_telur',
        'jumlah_menetas',
        'survival_rate',
        'jenis_ikan',
        'fase_pertumbuhan',
        'jumlah_kematian',
        'total_bobot_kg',
        'status',
    ];

    /**
     * Hitung Survival Rate (SR) Pembibitan Otomatis.
     * Rumus SOP: (Jumlah Menetas / Total Telur Awal) * 100%
     */
    public function calculateSurvivalRate(): float
    {
        $telur = (int) ($this->jumlah_telur > 0 ? $this->jumlah_telur : $this->jumlah_bibitAwal);
        $menetas = (int) ($this->jumlah_menetas > 0 ? $this->jumlah_menetas : max(0, $telur - (int)$this->jumlah_kematian));

        if ($telur <= 0) {
            return 0.0;
        }

        return round(($menetas / $telur) * 100, 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kolam()
    {
        return $this->belongsTo(Kolam::class, 'id_kolam', 'id_kolam');
    }

    public function ikan()
    {
        return $this->belongsTo(Ikan::class, 'id_ikan', 'id_ikan');
    }

    public function batchPembesaran()
    {
        return $this->hasMany(BatchPembesaran::class, 'id_batch_pembibitan', 'id_batch');
    }
}
