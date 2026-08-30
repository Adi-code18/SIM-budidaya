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
        'tgl_tebar',
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
}
