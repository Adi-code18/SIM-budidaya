<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamplingPembesaran extends Model
{
    use HasFactory;

    protected $table = 'sampling_pembesaran';
    protected $primaryKey = 'id_sampling';

    protected $fillable = [
        'id_pembesaran',
        'id_user',
        'tgl_sampling',
        'jumlah_sample_ekor',
        'total_bobot_sample_gr',
        'abw_gram',
        'biomassa_koreksi_kg',
        'catatan',
    ];

    protected $casts = [
        'tgl_sampling'          => 'date',
        'jumlah_sample_ekor'    => 'integer',
        'total_bobot_sample_gr' => 'float',
        'abw_gram'              => 'float',
        'biomassa_koreksi_kg'   => 'float',
    ];

    public function batchPembesaran()
    {
        return $this->belongsTo(BatchPembesaran::class, 'id_pembesaran', 'id_pembesaran');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
