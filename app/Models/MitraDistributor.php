<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraDistributor extends Model
{
    use HasFactory;

    protected $table = 'mitra_distributor';
    protected $primaryKey = 'id_mitra';

    protected $fillable = [
        'id_user',
        'nama_mitra',
        'tipe_mitra',
        'logo_mitra',
        'alamat',
        'longitude',
        'latitude',
    ];

    /**
     * URL resolusi untuk logo/gambar mitra.
     */
    public function getLogoUrlAttribute(): string
    {
        if (!empty($this->logo_mitra)) {
            if (str_starts_with($this->logo_mitra, 'http://') || str_starts_with($this->logo_mitra, 'https://')) {
                return $this->logo_mitra;
            }
            return asset('storage/' . $this->logo_mitra);
        }

        return 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=120';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function transaksiDistribusi()
    {
        return $this->hasMany(TransaksiDistribusi::class, 'id_mitra', 'id_mitra');
    }

    public function pembelianPakan()
    {
        return $this->hasMany(PembelianPakan::class, 'id_mitra', 'id_mitra');
    }
}
