<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogKualitasAir extends Model
{
    use HasFactory;

    protected $table = 'log_kualitas_air';
    protected $primaryKey = 'id_log_air';

    protected $fillable = [
        'id_kolam',
        'id_user',
        'tgl_catat',
        'suhu_air',
        'ph_air',
        'kondisi_air',
        'catatan',
    ];

    protected $casts = [
        'tgl_catat' => 'datetime',
        'suhu_air'  => 'float',
        'ph_air'    => 'float',
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class, 'id_kolam', 'id_kolam');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Helper cek apakah kondisi air berada pada batas kritis standar
     */
    public function getIsKritisAttribute(): bool
    {
        // pH aman budidaya ikan air tawar: 6.5 - 8.5
        if ($this->ph_air !== null && ($this->ph_air < 6.5 || $this->ph_air > 8.5)) {
            return true;
        }
        // Suhu aman budidaya: 25 - 32 C
        if ($this->suhu_air !== null && ($this->suhu_air < 24.0 || $this->suhu_air > 33.0)) {
            return true;
        }
        if (in_array($this->kondisi_air, ['keruh', 'berbau'])) {
            return true;
        }
        return false;
    }
}
