<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDistribusi extends Model
{
    use HasFactory;

    protected $table = 'transaksi_distribusi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'no_surat_jalan',
        'id_user',
        'id_mitra',
        'id_pembesaran',
        'tanggal_order',
        'Total_kg',
        'harga_total',
        'status_order',
        'waktu_mulai_pemberokan',
        'waktu_selesai_pemberokan',
        'durasi_pemberokan_jam',
        'grading_size',
        'catatan_grading',
        'Jenis_order',
        'Bukti_sampai',
    ];

    protected $casts = [
        'waktu_mulai_pemberokan'   => 'datetime',
        'waktu_selesai_pemberokan' => 'datetime',
        'tanggal_order'            => 'date',
    ];

    /**
     * Generate Nomor Surat Jalan Resmi berformat SJ/YYYY/MM/XXXX
     */
    public static function generateNoSuratJalan(?int $transaksiId = null): string
    {
        $year = date('Y');
        $month = date('m');
        $id = $transaksiId ?? (self::max('id_transaksi') ? self::max('id_transaksi') + 1 : 1);
        $padded = str_pad($id, 3, '0', STR_PAD_LEFT);

        return "SJ/{$year}/{$month}/{$padded}";
    }

    /**
     * Dapatkan nomor surat jalan atau buat jika belum ada.
     */
    public function getOrGenerateSuratJalan(): string
    {
        if (!empty($this->no_surat_jalan)) {
            return $this->no_surat_jalan;
        }

        $no = self::generateNoSuratJalan($this->id_transaksi);
        $this->no_surat_jalan = $no;
        $this->save();

        return $no;
    }

    /**
     * Cek apakah timer pemberokan masih aktif.
     */
    public function isPemberokanActive(): bool
    {
        if (in_array($this->status_order, ['pemberokan', 'pemberokian']) && $this->waktu_selesai_pemberokan) {
            return now()->lt($this->waktu_selesai_pemberokan);
        }
        return false;
    }

    /**
     * Sisa durasi pemberokan dalam menit (0 jika sudah selesai).
     */
    public function getSisaPemberokanMenit(): int
    {
        if ($this->waktu_selesai_pemberokan && now()->lt($this->waktu_selesai_pemberokan)) {
            return (int) now()->diffInMinutes($this->waktu_selesai_pemberokan, false);
        }
        return 0;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function mitra()
    {
        return $this->belongsTo(MitraDistributor::class, 'id_mitra', 'id_mitra');
    }

    public function batchPembesaran()
    {
        return $this->belongsTo(BatchPembesaran::class, 'id_pembesaran', 'id_pembesaran');
    }
}
