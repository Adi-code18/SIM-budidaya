<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingSetting extends Model
{
    protected $table = 'landing_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Nilai default konfigurasi tema dan konten Landing Page
     */
    public static function defaults(): array
    {
        return [
            // Branding & Global Kontak
            'theme_color'       => 'itb_royal', // itb_royal | marine_emerald | cyber_navy | sunset_amber
            'brand_name'        => 'AQUAFARM PRIANGAN',
            'brand_subtitle'    => 'Fasilitas Budidaya Air Tawar Ciamis',
            'phone_wa'          => '081234567890',
            'wa_template_text'  => 'Halo Aquafarm Priangan, saya tertarik memesan pasokan ikan panen segar untuk resto/usaha saya.',
            'harvest_hours'     => 'Pagi: 05.00 – 08.00 WIB | Sore: 16.00 – 18.00 WIB',
            'show_calculator'   => '1',
            'show_agenda'       => '1',
            'show_investor'     => '1',

            // SLIDE 1 (Akreditasi & Prestasi Utama ITB Style)
            'hero_rank'         => '#1',
            'hero_headline'     => 'TERAKREDITASI #1 DI JAWA BARAT',
            'hero_description'  => 'Fasilitas Akuakultur Terpadu & Pemasok Utama Ikan Air Tawar Bebas Bau Lumpur (Zero Geosmin) Melalui Standar Pemberokan 24 Jam.',
            'slide1_tag'        => 'TERAKREDITASI CBIB PRIMA 1',
            'slide1_subbrand'   => 'satu priangan',
            'slide1_subheadline'=> 'KOMODITAS AIR TAWAR • NILA MERAH • LELE • PATIN • GURAME',
            'slide1_badge1_title'=> 'MUTU KKP',
            'slide1_badge1_val'  => '#1',
            'slide1_badge1_sub'  => 'CBIB PRIMA 1',
            'slide1_badge2_title'=> 'AIR DERAS',
            'slide1_badge2_val'  => '0%',
            'slide1_badge2_sub'  => 'ZERO GEOSMIN',
            'slide1_badge3_title'=> 'LOGISTIK DDP',
            'slide1_badge3_val'  => '≤2%',
            'slide1_badge3_sub'  => 'SUSUT HIDUP',
            'hero_image'        => null,

            // SLIDE 2 (Fasilitas Tambak & Air Deras)
            'slide2_tag'        => 'FASILITAS BUNI SEURI CIAMIS',
            'slide2_title'      => 'Sirkulasi Air Mengalir Deras 24 Jam & Kolam Bioflok Modern',
            'slide2_desc'       => 'Memanfaatkan sumber air pegunungan alami yang mengalir deras dan beroksigen tinggi. Standar pemuasaan 24 jam menjamin daging ikan padat, gurih alami, dan bebas bau lumpur (Zero Geosmin).',
            'slide2_btn_text'   => 'Pelajari Standar Mutu Budidaya',
            'slide2_btn_link'   => '#sop',
            'hero_image_2'      => null,

            // SLIDE 3 (Panen Segar & Grading)
            'slide3_tag'        => 'PANEN SEGAR AIR TAWAR',
            'slide3_title'      => 'Grading Ketat Komoditas Unggulan Siap Konsumsi',
            'slide3_desc'       => 'Nila Merah Nirwana, Lele Sangkuriang, Patin Siam, dan Gurame Soang dipanen segar setiap pagi dan sore hari. Daging padat manis tanpa residu antibiotik dan siap diolah dapur kuliner Anda.',
            'slide3_btn_text'   => 'Lihat Katalog Komoditas Ikan',
            'slide3_btn_link'   => '#katalog',
            'hero_image_3'      => null,

            // SLIDE 4 (Riset & Mutu Laboratorium)
            'slide4_tag'        => 'STANDAR CBIB PRIMA 1 KKP RI',
            'slide4_title'      => 'Monitoring Kualitas Air Real-Time & Uji Mikrobiologi',
            'slide4_desc'       => 'Pengujian parameter pH, Dissolved Oxygen (DO), suhu, dan nitrit dilakukan berkala oleh tenaga ahli budidaya berpengalaman. Menjamin ikan hidup sehat secara alami tanpa penambahan zat kimia sintetis berbahaya.',
            'slide4_btn_text'   => 'Verifikasi Sertifikasi Mutu',
            'slide4_btn_link'   => 'wa',
            'hero_image_4'      => null,

            // SLIDE 5 (Mitra Kuliner Restoran)
            'slide5_tag'        => 'PASOKAN RESTORAN & GROSIR',
            'slide5_title'      => 'Pilihan Utama 50+ Rumah Makan & Hotel Jawa Barat',
            'slide5_desc'       => 'Jaminan kontinuitas pasokan harian tanpa putus stok. Dapatkan kontrak pasok rutin dengan harga stabil, penjadwalan panen subuh, dan spesifikasi bobot yang sesuai dengan menu andalan resto Anda.',
            'slide5_btn_text'   => 'Simulasi Pesanan Restoran',
            'slide5_btn_link'   => '#kalkulator',
            'hero_image_5'      => null,

            // SLIDE 6 (Panorama Tambak & Armada)
            'slide6_tag'        => 'LOGISTIK KHUSUS AIR BER-OKSIGEN',
            'slide6_title'      => 'Armada Aerasi Cepat Menjangkau Seluruh Jawa Barat',
            'slide6_desc'       => 'Dilengkapi box aerasi motor untuk pengiriman cepat lokal Priangan Timur, pickup tangki drum aerasi aktif, hingga truk tangki oksigen murni untuk partai tonase ke Bandung Raya dan Jabodetabek.',
            'slide6_btn_text'   => 'Konsultasi Rute & Jadwal Armada',
            'slide6_btn_link'   => 'wa',
            'hero_image_6'      => null,

            // Legacy keys for compatibility
            'badge_title'       => 'STANDAR RESMI KEMENTERIAN KELAUTAN & PERIKANAN',
            'badge_desc'        => 'Terverifikasi Bebas Residu Kimia, Daging Padat & Garansi DDP Susut Berat ≤ 2%.',
        ];
    }

    /**
     * Definisi palet tema yang tersedia
     */
    public static function themes(): array
    {
        return [
            'itb_royal' => [
                'name'        => 'ITB Royal Blue & Gold (Default)',
                'description' => 'Gaya resmi institusional ITB dengan biru royal mendalam dan aksen kuning keemasan.',
                'topbar_bg'   => 'bg-[#081a3e]',
                'nav_bg'      => 'bg-[#004b93]',
                'hero_from'   => 'from-[#0d2a6b] via-[#092257] to-[#06173d]',
                'accent_hex'  => '#FFC700',
                'gold_hex'    => '#ff8f00',
                'badge_color' => 'bg-amber-400 text-slate-900',
                'preview_css' => 'background: linear-gradient(135deg, #004b93 0%, #0d2a6b 60%, #06173d 100%);'
            ],
            'marine_emerald' => [
                'name'        => 'Marine Aquacultural Emerald',
                'description' => 'Nuansa hijau tosca perairan alami dan emas segar, melambangkan ekosistem perikanan bersih.',
                'topbar_bg'   => 'bg-[#06261c]',
                'nav_bg'      => 'bg-[#064e3b]',
                'hero_from'   => 'from-[#064e3b] via-[#022c22] to-[#01140e]',
                'accent_hex'  => '#34D399',
                'gold_hex'    => '#10B981',
                'badge_color' => 'bg-emerald-400 text-slate-900',
                'preview_css' => 'background: linear-gradient(135deg, #064e3b 0%, #022c22 60%, #01140e 100%);'
            ],
            'cyber_navy' => [
                'name'        => 'High-Tech Deep Slate & Cyan',
                'description' => 'Nuansa futuristik modern IoT budidaya dengan deep slate hitam dan aksen cyan neon menyala.',
                'topbar_bg'   => 'bg-[#020617]',
                'nav_bg'      => 'bg-[#0f172a]',
                'hero_from'   => 'from-[#0f172a] via-[#090d16] to-[#020617]',
                'accent_hex'  => '#38BDF8',
                'gold_hex'    => '#0EA5E9',
                'badge_color' => 'bg-sky-400 text-slate-900',
                'preview_css' => 'background: linear-gradient(135deg, #0f172a 0%, #090d16 60%, #020617 100%);'
            ],
            'sunset_amber' => [
                'name'        => 'Warm Sunset Amber & Bronze',
                'description' => 'Kombinasi warna senja keemasan saat panen melimpah dengan kehangatan terpercaya.',
                'topbar_bg'   => 'bg-[#291307]',
                'nav_bg'      => 'bg-[#7c2d12]',
                'hero_from'   => 'from-[#7c2d12] via-[#431407] to-[#1c0803]',
                'accent_hex'  => '#FBBF24',
                'gold_hex'    => '#EA580C',
                'badge_color' => 'bg-amber-400 text-slate-900',
                'preview_css' => 'background: linear-gradient(135deg, #7c2d12 0%, #431407 60%, #1c0803 100%);'
            ],
        ];
    }

    /**
     * Ambil seluruh konfigurasi aktif
     */
    public static function getSettings(): array
    {
        $defaults = self::defaults();
        $stored = self::pluck('value', 'key')->toArray();

        $merged = array_merge($defaults, $stored);

        // Pasang theme palette aktif
        $themeKey = $merged['theme_color'] ?? 'itb_royal';
        $themes = self::themes();
        $merged['theme'] = $themes[$themeKey] ?? $themes['itb_royal'];

        // URL Foto Hero (Slide 1)
        if (!empty($merged['hero_image']) && Storage::disk('public')->exists($merged['hero_image'])) {
            $merged['hero_image_url'] = asset('storage/' . $merged['hero_image']);
        } else {
            $merged['hero_image_url'] = file_exists(public_path('images/icon distribusi.png')) 
                ? asset('images/icon distribusi.png') 
                : 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80';
        }

        // URL Slide 2 (Fasilitas Bioflok & Tambak Ciamis)
        if (!empty($merged['hero_image_2']) && Storage::disk('public')->exists($merged['hero_image_2'])) {
            $merged['hero_slide2_url'] = asset('storage/' . $merged['hero_image_2']);
        } else {
            $merged['hero_slide2_url'] = file_exists(public_path('images/slider_fasilitas.jpg'))
                ? asset('images/slider_fasilitas.jpg')
                : 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80';
        }

        // URL Slide 3 (Panen Segar Ikan Nila & Gurame)
        if (!empty($merged['hero_image_3']) && Storage::disk('public')->exists($merged['hero_image_3'])) {
            $merged['hero_slide3_url'] = asset('storage/' . $merged['hero_image_3']);
        } else {
            $merged['hero_slide3_url'] = file_exists(public_path('images/slider_panen.jpg'))
                ? asset('images/slider_panen.jpg')
                : 'https://images.unsplash.com/photo-1524704654690-b56c05c78a00?auto=format&fit=crop&w=1200&q=80';
        }

        // URL Slide 4 (Riset Laboratorium Kualitas Air & Sertifikasi CBIB)
        if (!empty($merged['hero_image_4']) && Storage::disk('public')->exists($merged['hero_image_4'])) {
            $merged['hero_slide4_url'] = asset('storage/' . $merged['hero_image_4']);
        } else {
            $merged['hero_slide4_url'] = file_exists(public_path('images/collage_lab.jpg'))
                ? asset('images/collage_lab.jpg')
                : 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80';
        }

        // URL Slide 5 (Pasokan Mitra Kuliner & Restoran Siap Saji)
        if (!empty($merged['hero_image_5']) && Storage::disk('public')->exists($merged['hero_image_5'])) {
            $merged['hero_slide5_url'] = asset('storage/' . $merged['hero_image_5']);
        } else {
            $merged['hero_slide5_url'] = file_exists(public_path('images/collage_resto.jpg'))
                ? asset('images/collage_resto.jpg')
                : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80';
        }

        // URL Slide 6 (Panorama Kawasan Tambak Sunrise Priangan)
        if (!empty($merged['hero_image_6']) && Storage::disk('public')->exists($merged['hero_image_6'])) {
            $merged['hero_slide6_url'] = asset('storage/' . $merged['hero_image_6']);
        } else {
            $merged['hero_slide6_url'] = file_exists(public_path('images/slider_sunrise.jpg'))
                ? asset('images/slider_sunrise.jpg')
                : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80';
        }

        // URL Asset Collage Quad-Photo Slide 1
        $merged['collage_img1'] = file_exists(public_path('images/slider_fasilitas.jpg')) ? asset('images/slider_fasilitas.jpg') : $merged['hero_slide2_url'];
        $merged['collage_img2'] = file_exists(public_path('images/slider_panen.jpg')) ? asset('images/slider_panen.jpg') : $merged['hero_slide3_url'];
        $merged['collage_img3'] = file_exists(public_path('images/collage_lab.jpg')) ? asset('images/collage_lab.jpg') : $merged['hero_slide4_url'];
        $merged['collage_img4'] = file_exists(public_path('images/collage_resto.jpg')) ? asset('images/collage_resto.jpg') : $merged['hero_slide5_url'];
        $merged['collage_distribusi'] = file_exists(public_path('images/icon distribusi.png')) ? asset('images/icon distribusi.png') : $merged['hero_image_url'];

        // Sanitasi nomor WA
        $cleanPhone = preg_replace('/[^0-9]/', '', $merged['phone_wa']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        $merged['phone_wa_clean'] = $cleanPhone ?: '6281234567890';

        return $merged;
    }

    /**
     * Simpan / perbarui batch pengaturan
     */
    public static function saveSettings(array $data): void
    {
        foreach ($data as $key => $val) {
            self::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($val) ? ($val ? '1' : '0') : (string) $val]
            );
        }
    }
}
