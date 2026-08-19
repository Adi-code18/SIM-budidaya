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
        'jumlah_kematian',
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
}
