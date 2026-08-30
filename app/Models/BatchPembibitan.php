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
        'tgl_pemijahan',
        'jumlah_bibitAwal',
        'jenis_ikan',
        'fase_pertumbuhan',
        'jumlah_kematian',
        'total_bobot_kg',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kolam()
    {
        return $this->belongsTo(Kolam::class, 'id_kolam', 'id_kolam');
    }

    public function batchPembesaran()
    {
        return $this->hasMany(BatchPembesaran::class, 'id_batch_pembibitan', 'id_batch');
    }
}
