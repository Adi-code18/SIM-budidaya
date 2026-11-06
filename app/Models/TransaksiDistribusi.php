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
        'metode_pembayaran',
        'status_pembayaran',
        'termin_hari',
        'tgl_jatuh_tempo',
        'toleransi_susut_persen',
        'susut_diluar_toleransi_kg',
        'potongan_klaim_rp',
        'total_tagihan_akhir',
        'jumlah_terbayar',
        'tgl_lunas',
        'catatan_klaim',
        'status_order',
        'waktu_mulai_pemberokan',
        'waktu_selesai_pemberokan',
        'durasi_pemberokan_jam',
        'grading_size',
        'catatan_grading',
        'Jenis_order',
        'Bukti_sampai',
        'tgl_rencana_kirim',
        'tipe_panen',
        'berat_aktual_kg',
        'susut_bobot_kg',
        'mortalitas_antar_kg',
    ];

    protected $casts = [
        'waktu_mulai_pemberokan'   => 'datetime',
        'waktu_selesai_pemberokan' => 'datetime',
        'tanggal_order'            => 'date',
        'tgl_rencana_kirim'        => 'date',
        'tgl_jatuh_tempo'          => 'date',
        'tgl_lunas'                => 'date',
        'termin_hari'              => 'integer',
        'Total_kg'                 => 'float',
        'harga_total'              => 'float',
        'berat_aktual_kg'          => 'float',
        'susut_bobot_kg'           => 'float',
        'mortalitas_antar_kg'      => 'float',
        'toleransi_susut_persen'   => 'float',
        'susut_diluar_toleransi_kg'=> 'float',
        'potongan_klaim_rp'        => 'float',
        'total_tagihan_akhir'      => 'float',
        'jumlah_terbayar'          => 'float',
    ];

    protected $appends = [
        'bukti_sampai_url',
        'persentase_susut',
        'is_ddp_aman',
        'sisa_piutang',
        'is_jatuh_tempo',
        'tagihan_riil',
    ];

    /**
     * Hitung sisa piutang / tagihan yang belum dibayar oleh mitra
     */
    public function getSisaPiutangAttribute(): float
    {
        $tagihan = $this->getTagihanRiilAttribute();
        return max(0, $tagihan - (float)$this->jumlah_terbayar);
    }

    /**
     * Tagihan riil yang sah setelah dikurangi potongan klaim susut
     */
    public function getTagihanRiilAttribute(): float
    {
        if ($this->total_tagihan_akhir > 0) {
            return (float) $this->total_tagihan_akhir;
        }
        return max(0, (float)$this->harga_total - (float)$this->potongan_klaim_rp);
    }

    /**
     * Cek apakah piutang sudah melewati batas tanggal jatuh tempo
     */
    public function getIsJatuhTempoAttribute(): bool
    {
        if ($this->status_pembayaran === 'lunas' || $this->getSisaPiutangAttribute() <= 0) {
            return false;
        }
        if (!$this->tgl_jatuh_tempo) {
            return false;
        }
        return \Carbon\Carbon::parse($this->tgl_jatuh_tempo)->isPast();
    }

    /**
     * Rekalkulasi klaim susut timbangan & tagihan akhir
     */
    public function recalculateKlaimSusut(): void
    {
        $totalKg = (float) $this->Total_kg;
        $hargaTotal = (float) $this->harga_total;
        $susutBobot = (float) $this->susut_bobot_kg;
        $mortalitas = (float) $this->mortalitas_antar_kg;
        $totalSusut = $susutBobot + $mortalitas;

        $toleransiPersen = (float) ($this->toleransi_susut_persen > 0 ? $this->toleransi_susut_persen : 2.0);
        $toleransiKg = round($totalKg * ($toleransiPersen / 100), 2);

        $susutKelebihan = max(0, round($totalSusut - $toleransiKg, 2));
        $hargaPerKg = $totalKg > 0 ? round($hargaTotal / $totalKg, 2) : 0;
        $potonganKlaim = round($susutKelebihan * $hargaPerKg, 2);

        $this->toleransi_susut_persen = $toleransiPersen;
        $this->susut_diluar_toleransi_kg = $susutKelebihan;
        $this->potongan_klaim_rp = $potonganKlaim;
        $this->total_tagihan_akhir = max(0, $hargaTotal - $potonganKlaim);

        // Jika sudah bayar lunas atau tunai
        if ($this->metode_pembayaran === 'tunai' || $this->metode_pembayaran === 'transfer') {
            if ($this->jumlah_terbayar <= 0) {
                $this->jumlah_terbayar = $this->total_tagihan_akhir;
                $this->status_pembayaran = 'lunas';
                $this->tgl_lunas = $this->tgl_lunas ?: now()->toDateString();
            }
        } elseif ($this->metode_pembayaran === 'tempo') {
            if ($this->jumlah_terbayar >= $this->total_tagihan_akhir && $this->total_tagihan_akhir > 0) {
                $this->status_pembayaran = 'lunas';
                $this->tgl_lunas = $this->tgl_lunas ?: now()->toDateString();
            } elseif ($this->jumlah_terbayar > 0) {
                $this->status_pembayaran = 'sebagian';
            } else {
                $this->status_pembayaran = ($this->tgl_jatuh_tempo && \Carbon\Carbon::parse($this->tgl_jatuh_tempo)->isPast()) ? 'menunggak' : 'tempo';
            }
        }
    }

    /**
     * Hitung persentase susut / mortalitas pengiriman terhadap bobot pesanan
     */
    public function getPersentaseSusutAttribute(): float
    {
        if ($this->Total_kg > 0 && ($this->susut_bobot_kg || $this->mortalitas_antar_kg)) {
            $totalSusut = (float) ($this->susut_bobot_kg + $this->mortalitas_antar_kg);
            return round(($totalSusut / (float) $this->Total_kg) * 100, 2);
        }
        return 0.0;
    }

    /**
     * Cek apakah susut/mortalitas berada di dalam batas toleransi DDP (< 2%)
     */
    public function getIsDdpAmanAttribute(): bool
    {
        return $this->persentase_susut <= 2.0;
    }

    /**
     * Dapatkan URL publik untuk foto bukti serah terima / penerimaan barang.
     */
    public function getBuktiSampaiUrlAttribute(): ?string
    {
        if (empty($this->Bukti_sampai)) {
            return null;
        }

        if (str_starts_with($this->Bukti_sampai, 'http://') || str_starts_with($this->Bukti_sampai, 'https://')) {
            return $this->Bukti_sampai;
        }

        return asset('storage/' . ltrim($this->Bukti_sampai, '/'));
    }

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
