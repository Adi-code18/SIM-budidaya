<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanLibur extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_libur';
    protected $primaryKey = 'id_libur';

    protected $fillable = [
        'id_user',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'status_pengajuan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
