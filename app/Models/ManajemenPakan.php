<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManajemenPakan extends Model
{
    use HasFactory;

    protected $table = 'manajemen_pakan';
    protected $primaryKey = 'id_pakan';

    protected $fillable = [
        'id_user',
        'id_kolam',
        'tgl_log',
        'kg_pelet',
        'kg_daun',
        'jenis_daun',
        'total_biaya',
        'ph_air',
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
