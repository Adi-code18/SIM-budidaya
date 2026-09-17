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

    public function batchPembibitan()
    {
        return $this->hasMany(BatchPembibitan::class, 'id_ikan', 'id_ikan');
    }
}
