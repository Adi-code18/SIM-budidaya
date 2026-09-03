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
    ];

    public function batchPembibitan()
    {
        return $this->hasMany(BatchPembibitan::class, 'id_ikan', 'id_ikan');
    }
}
