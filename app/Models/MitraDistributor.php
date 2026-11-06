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
        'plafon_kredit',
        'termin_default_hari',
        'longitude',
        'latitude',
    ];

    protected $casts = [
        'plafon_kredit'       => 'float',
        'termin_default_hari' => 'integer',
        'longitude'           => 'float',
        'latitude'            => 'float',
    ];

    protected $appends = [
        'logo_url',
        'total_piutang_berjalan',
        'sisa_plafon',
    ];

    /**
     * Total piutang yang belum terbayar oleh mitra ini
     */
    public function getTotalPiutangBerjalanAttribute(): float
    {
        return (float) $this->transaksiDistribusi()
            ->whereIn('status_pembayaran', ['tempo', 'menunggak', 'sebagian'])
            ->get()
            ->sum(function ($trx) {
                return $trx->sisa_piutang;
            });
    }

    /**
     * Sisa plafon kredit yang masih dapat digunakan
     */
    public function getSisaPlafonAttribute(): float
    {
        $plafon = (float) ($this->plafon_kredit > 0 ? $this->plafon_kredit : 20000000.0);
        return max(0, $plafon - $this->total_piutang_berjalan);
    }

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
