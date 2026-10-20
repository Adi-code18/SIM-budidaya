<?php

namespace App\Http\Controllers;

use App\Models\BatchPembibitan;
use App\Models\BatchPembesaran;
use App\Models\Ikan;
use App\Models\Kolam;
use App\Models\LandingSetting;
use App\Models\User;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Tampilkan Halaman Utama / Landing Page Perusahaan
     */
    public function index()
    {
        // 0. Ambil konfigurasi tema & konten kustom landing page
        $settings = LandingSetting::getSettings();

        // 1. Ambil data komoditas ikan dengan penghitungan kolam & fase aktif riil
        $ikans = Ikan::all()->map(function ($ikan) {
            $cleanName = trim(str_ireplace('ikan', '', $ikan->nama_ikan));

            // Hitung batch pembibitan aktif
            $bibitQuery = BatchPembibitan::where(function($q) use ($ikan, $cleanName) {
                $q->where('id_ikan', $ikan->id_ikan)
                  ->orWhere('jenis_ikan', 'like', "%{$cleanName}%");
            })->where('status', '!=', 'selesai');
            $bibitCount = $bibitQuery->count();
            $kolamBibit = (int) $bibitQuery->distinct('id_kolam')->count('id_kolam');

            // Hitung batch pembesaran aktif
            $pembesaranQuery = BatchPembesaran::where(function($q) use ($ikan, $cleanName) {
                $q->where('jenis_ikan', 'like', "%{$cleanName}%")
                  ->orWhereHas('batchPembibitan', function($sub) use ($ikan) {
                      $sub->where('id_ikan', $ikan->id_ikan);
                  });
            })->whereIn('status_siklus', ['aktif', 'berjalan', 'siap_panen']);
            $pembesaranCount = $pembesaranQuery->count();
            $kolamPembesaran = (int) $pembesaranQuery->distinct('id_kolam')->count('id_kolam');

            $ikan->total_fase_aktif = $bibitCount + $pembesaranCount;
            $ikan->total_kolam_aktif = $kolamBibit + $kolamPembesaran;

            // Berikan metadata deskriptif autentik khas budidaya air tawar
            $meta = $this->getIkanMetadata($ikan->nama_ikan);
            $ikan->tagline = $meta['tagline'];
            $ikan->keunggulan = $meta['keunggulan'];
            $ikan->porsi_pasar = $meta['porsi_pasar'];
            $ikan->stok_status = $meta['stok_status'];
            $ikan->badge_color = $meta['badge_color'];
            $ikan->bg_gradient = $meta['bg_gradient'];
            return $ikan;
        })->sortByDesc(function ($ikan) {
            // Urutkan prioritas: kolam aktif terbanyak, lalu total fase budidaya aktif
            return ($ikan->total_kolam_aktif * 100) + $ikan->total_fase_aktif;
        })->values();

        // 2. Metrik operasional riil tambak
        $totalKolam = Kolam::count();
        $kolamTerisi = Kolam::where('status', 'terisi')->count();
        $totalBiomassaAktif = (float) BatchPembesaran::where('status_siklus', 'aktif')->sum('biomassa_est');
        $totalPanenSelesai = (float) BatchPembesaran::where('status_siklus', 'selesai')->sum('jumlah_panen_kg');

        // Jika data panen seeder masih 0, fallback nilai statistik minimum representatif
        $displayBiomassa = $totalBiomassaAktif > 0 ? $totalBiomassaAktif : 4250;
        $displayTotalPanen = $totalPanenSelesai > 0 ? $totalPanenSelesai : 18500;
        $displayKolam = $totalKolam > 0 ? $totalKolam : 12;

        // 3. Kontak Resmi Tambak
        $manager = User::where('role', 'manajer')->first();
        $rawPhone = $settings['phone_wa'] ?? ($manager->no_tlp ?? '081234567890');
        $cleanPhone = $settings['phone_wa_clean'] ?? '6281234567890';

        $company = [
            'name'         => $settings['brand_name'] ?? 'Aquafarm Priangan',
            'legal_name'   => 'PT AMS Budidaya Nusantara',
            'phone_raw'    => $rawPhone,
            'phone_wa'     => $cleanPhone,
            'email'        => $manager->email ?? 'sales@aquafarm.id',
            'address'      => $manager->alamat ?? 'Jl. Hj. Nurjamil Blok Panyaweuyan Desa Buniseuri Kec. Cipaku, Ciamis, Jawa Barat',
            'harvest_time' => $settings['harvest_hours'] ?? 'Pagi: 05.00 – 08.00 WIB | Sore: 16.00 – 18.00 WIB',
            'fcr_avg'      => '1.18',
            'sr_rate'      => '88.5%',
            'ddp_mortality'=> '< 1.8%',
        ];

        return view('landing.index', compact('ikans', 'company', 'displayKolam', 'displayBiomassa', 'displayTotalPanen', 'settings'));
    }

    /**
     * Helper metadata spesifikasi mutu ikan air tawar konsumsi
     */
    private function getIkanMetadata(string $namaIkan): array
    {
        $namaLower = strtolower($namaIkan);

        if (str_contains($namaLower, 'nila')) {
            return [
                'tagline'      => 'Strain Nirwana Super • Daging Tebal & Gurih Alami',
                'keunggulan'   => ['Pemuasaan/Pemberokan 24 Jam di air deras (Bebas Bau Lumpur)', 'Daging putih padat, minim susut saat digoreng/bakar', 'Grade Restoran & Katering Spesial'],
                'porsi_pasar'  => 'Restoran Sunda, Rumah Makan Padang & Seafood',
                'stok_status'  => 'Siap Panen Mingguan',
                'badge_color'  => 'bg-rose-50 text-rose-700 border-rose-200',
                'bg_gradient'  => 'from-rose-500/10 via-sky-500/5 to-transparent',
            ];
        }

        if (str_contains($namaLower, 'lele')) {
            return [
                'tagline'      => 'Sangkuriang F1 • Kulit Mulus, Lendir Normal & Lincah',
                'keunggulan'   => ['Pelet protein 30% + probiotik bioflok lacto', 'Tidak mudah patah/lembek saat dimasak', 'Insang merah segar & tubuh bersih bebas luka'],
                'porsi_pasar'  => 'Pecel Lele, Lapak Pasar Tradisional & Industri Olahan',
                'stok_status'  => 'Ready Stock Setiap Hari',
                'badge_color'  => 'bg-amber-50 text-amber-700 border-amber-200',
                'bg_gradient'  => 'from-amber-500/10 via-sky-500/5 to-transparent',
            ];
        }

        if (str_contains($namaLower, 'patin')) {
            return [
                'tagline'      => 'Pangasius Siam Grade A • Lemak Terkontrol & Daging Lembut',
                'keunggulan'   => ['Sistem kolam sirkulasi air tenang minim limbah', 'Rendah kadar lemak kuning, aroma daging segar', 'Ideal untuk fillet beku, sup asam pedas & pindang'],
                'porsi_pasar'  => 'Hotel, Catering Pindang, Pabrik Olahan Fillet',
                'stok_status'  => 'Siklus Panen Rutin',
                'badge_color'  => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                'bg_gradient'  => 'from-cyan-500/10 via-sky-500/5 to-transparent',
            ];
        }

        if (str_contains($namaLower, 'gurame')) {
            return [
                'tagline'      => 'Gurame Soang Priangan • Rasa Manis Daging Juara',
                'keunggulan'   => ['Pakan mix pelet + daun talas & pepaya fermentasi', 'Tekstur daging berserat kokoh, sangat manis', 'Perlakuan panen dingin anti-stres tanpa luka sisik'],
                'porsi_pasar'  => 'Restoran Bintang & Rumah Makan Lesehan Keluarga',
                'stok_status'  => 'Kuota Terbatas (Pre-Order)',
                'badge_color'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'bg_gradient'  => 'from-emerald-500/10 via-sky-500/5 to-transparent',
            ];
        }

        return [
            'tagline'      => 'Komoditas Unggulan Air Tawar Berkualitas Tinggi',
            'keunggulan'   => ['Sistem budidaya terukur & higienis', 'Garansi hidup DDP sampai tujuan', 'Pemberokan terstandar'],
            'porsi_pasar'  => 'Mitra Pasar & Restoran Umum',
            'stok_status'  => 'Tersedia',
            'badge_color'  => 'bg-blue-50 text-blue-700 border-blue-200',
            'bg_gradient'  => 'from-blue-500/10 via-sky-500/5 to-transparent',
        ];
    }
}
