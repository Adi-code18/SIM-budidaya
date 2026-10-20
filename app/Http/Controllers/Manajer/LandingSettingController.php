<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingSettingController extends Controller
{
    /**
     * Tampilkan Halaman Editor Tema & Konten Landing Page
     */
    public function index()
    {
        $settings = LandingSetting::getSettings();
        $themes = LandingSetting::themes();

        return view('layouts.landing_setting.index', compact('settings', 'themes'));
    }

    /**
     * Simpan Perubahan Pengaturan Landing Page & Tema
     */
    public function update(Request $request)
    {
        $request->validate([
            'theme_color'      => 'required|string|in:itb_royal,marine_emerald,cyber_navy,sunset_amber',
            'brand_name'       => 'required|string|max:100',
            'brand_subtitle'   => 'nullable|string|max:150',
            'hero_rank'        => 'required|string|max:20',
            'hero_headline'    => 'required|string|max:100',
            'hero_description' => 'required|string|max:500',
            'badge_title'      => 'nullable|string|max:150',
            'badge_desc'       => 'nullable|string|max:255',
            'phone_wa'         => 'required|string|max:30',
            'wa_template_text' => 'nullable|string|max:300',
            'harvest_hours'    => 'nullable|string|max:150',
            'hero_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_2'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_3'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_4'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_5'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_6'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'show_calculator'  => 'nullable|boolean',
            'show_agenda'      => 'nullable|boolean',
            'show_investor'    => 'nullable|boolean',

            // Slide 1
            'slide1_tag'        => 'nullable|string|max:100',
            'slide1_subbrand'   => 'nullable|string|max:50',
            'slide1_subheadline'=> 'nullable|string|max:200',
            'slide1_badge1_title'=> 'nullable|string|max:50',
            'slide1_badge1_val'  => 'nullable|string|max:20',
            'slide1_badge1_sub'  => 'nullable|string|max:50',
            'slide1_badge2_title'=> 'nullable|string|max:50',
            'slide1_badge2_val'  => 'nullable|string|max:20',
            'slide1_badge2_sub'  => 'nullable|string|max:50',
            'slide1_badge3_title'=> 'nullable|string|max:50',
            'slide1_badge3_val'  => 'nullable|string|max:20',
            'slide1_badge3_sub'  => 'nullable|string|max:50',

            // Slide 2
            'slide2_tag'      => 'nullable|string|max:100',
            'slide2_title'    => 'nullable|string|max:150',
            'slide2_desc'     => 'nullable|string|max:500',
            'slide2_btn_text' => 'nullable|string|max:100',
            'slide2_btn_link' => 'nullable|string|max:200',

            // Slide 3
            'slide3_tag'      => 'nullable|string|max:100',
            'slide3_title'    => 'nullable|string|max:150',
            'slide3_desc'     => 'nullable|string|max:500',
            'slide3_btn_text' => 'nullable|string|max:100',
            'slide3_btn_link' => 'nullable|string|max:200',

            // Slide 4
            'slide4_tag'      => 'nullable|string|max:100',
            'slide4_title'    => 'nullable|string|max:150',
            'slide4_desc'     => 'nullable|string|max:500',
            'slide4_btn_text' => 'nullable|string|max:100',
            'slide4_btn_link' => 'nullable|string|max:200',

            // Slide 5
            'slide5_tag'      => 'nullable|string|max:100',
            'slide5_title'    => 'nullable|string|max:150',
            'slide5_desc'     => 'nullable|string|max:500',
            'slide5_btn_text' => 'nullable|string|max:100',
            'slide5_btn_link' => 'nullable|string|max:200',

            // Slide 6
            'slide6_tag'      => 'nullable|string|max:100',
            'slide6_title'    => 'nullable|string|max:150',
            'slide6_desc'     => 'nullable|string|max:500',
            'slide6_btn_text' => 'nullable|string|max:100',
            'slide6_btn_link' => 'nullable|string|max:200',
        ], [
            'theme_color.in'      => 'Pilihan tema visual tidak valid.',
            'brand_name.required' => 'Nama brand perusahaan wajib diisi.',
            'hero_rank.required'  => 'Label peringkat (misal #1) wajib diisi.',
            'hero_headline.required' => 'Judul headline hero wajib diisi.',
            'hero_description.required' => 'Deskripsi hero wajib diisi.',
            'phone_wa.required'   => 'Nomor kontak WhatsApp sales wajib diisi.',
            'hero_image.max'      => 'Ukuran foto banner 1 maksimal 5MB.',
            'hero_image_2.max'    => 'Ukuran foto banner 2 maksimal 5MB.',
            'hero_image_3.max'    => 'Ukuran foto banner 3 maksimal 5MB.',
            'hero_image_4.max'    => 'Ukuran foto banner 4 maksimal 5MB.',
            'hero_image_5.max'    => 'Ukuran foto banner 5 maksimal 5MB.',
            'hero_image_6.max'    => 'Ukuran foto banner 6 maksimal 5MB.',
        ]);

        $currentSettings = LandingSetting::getSettings();
        $dataToSave = [
            'theme_color'      => $request->theme_color,
            'brand_name'       => $request->brand_name,
            'brand_subtitle'   => $request->brand_subtitle ?? 'Fasilitas Budidaya Air Tawar Ciamis',
            'hero_rank'        => $request->hero_rank,
            'hero_headline'    => $request->hero_headline,
            'hero_description' => $request->hero_description,
            'badge_title'      => $request->badge_title ?? 'STANDAR RESMI KEMENTERIAN KELAUTAN & PERIKANAN',
            'badge_desc'       => $request->badge_desc ?? 'Terverifikasi Bebas Residu Kimia, Daging Padat & Garansi DDP Susut Berat ≤ 2%.',
            'phone_wa'         => $request->phone_wa,
            'wa_template_text' => $request->wa_template_text ?? 'Halo Aquafarm Priangan, saya tertarik memesan pasokan ikan panen segar.',
            'harvest_hours'    => $request->harvest_hours ?? 'Pagi: 05.00 – 08.00 WIB | Sore: 16.00 – 18.00 WIB',
            'show_calculator'  => $request->has('show_calculator') ? '1' : '0',
            'show_agenda'      => $request->has('show_agenda') ? '1' : '0',
            'show_investor'    => $request->has('show_investor') ? '1' : '0',

            // Slide 1 Custom Content
            'slide1_tag'        => $request->slide1_tag ?? 'TERAKREDITASI CBIB PRIMA 1',
            'slide1_subbrand'   => $request->slide1_subbrand ?? 'satu priangan',
            'slide1_subheadline'=> $request->slide1_subheadline ?? 'KOMODITAS AIR TAWAR • NILA MERAH • LELE • PATIN • GURAME',
            'slide1_badge1_title'=> $request->slide1_badge1_title ?? 'MUTU KKP',
            'slide1_badge1_val'  => $request->slide1_badge1_val ?? '#1',
            'slide1_badge1_sub'  => $request->slide1_badge1_sub ?? 'CBIB PRIMA 1',
            'slide1_badge2_title'=> $request->slide1_badge2_title ?? 'AIR DERAS',
            'slide1_badge2_val'  => $request->slide1_badge2_val ?? '0%',
            'slide1_badge2_sub'  => $request->slide1_badge2_sub ?? 'ZERO GEOSMIN',
            'slide1_badge3_title'=> $request->slide1_badge3_title ?? 'LOGISTIK DDP',
            'slide1_badge3_val'  => $request->slide1_badge3_val ?? '≤2%',
            'slide1_badge3_sub'  => $request->slide1_badge3_sub ?? 'SUSUT HIDUP',

            // Slide 2 Custom Content
            'slide2_tag'      => $request->slide2_tag ?? 'FASILITAS BUNI SEURI CIAMIS',
            'slide2_title'    => $request->slide2_title ?? 'Sirkulasi Air Mengalir Deras 24 Jam & Kolam Bioflok Modern',
            'slide2_desc'     => $request->slide2_desc ?? 'Memanfaatkan sumber air pegunungan alami yang mengalir deras dan beroksigen tinggi. Standar pemuasaan 24 jam menjamin daging ikan padat, gurih alami, dan bebas bau lumpur (Zero Geosmin).',
            'slide2_btn_text' => $request->slide2_btn_text ?? 'Pelajari Standar Mutu Budidaya',
            'slide2_btn_link' => $request->slide2_btn_link ?? '#sop',

            // Slide 3 Custom Content
            'slide3_tag'      => $request->slide3_tag ?? 'PANEN SEGAR AIR TAWAR',
            'slide3_title'    => $request->slide3_title ?? 'Grading Ketat Komoditas Unggulan Siap Konsumsi',
            'slide3_desc'     => $request->slide3_desc ?? 'Nila Merah Nirwana, Lele Sangkuriang, Patin Siam, dan Gurame Soang dipanen segar setiap pagi dan sore hari. Daging padat manis tanpa residu antibiotik dan siap diolah dapur kuliner Anda.',
            'slide3_btn_text' => $request->slide3_btn_text ?? 'Lihat Katalog Komoditas Ikan',
            'slide3_btn_link' => $request->slide3_btn_link ?? '#katalog',

            // Slide 4 Custom Content
            'slide4_tag'      => $request->slide4_tag ?? 'STANDAR CBIB PRIMA 1 KKP RI',
            'slide4_title'    => $request->slide4_title ?? 'Monitoring Kualitas Air Real-Time & Uji Mikrobiologi',
            'slide4_desc'     => $request->slide4_desc ?? 'Pengujian parameter pH, Dissolved Oxygen (DO), suhu, dan nitrit dilakukan berkala oleh tenaga ahli budidaya berpengalaman. Menjamin ikan hidup sehat secara alami tanpa penambahan zat kimia sintetis berbahaya.',
            'slide4_btn_text' => $request->slide4_btn_text ?? 'Verifikasi Sertifikasi Mutu',
            'slide4_btn_link' => $request->slide4_btn_link ?? 'wa',

            // Slide 5 Custom Content
            'slide5_tag'      => $request->slide5_tag ?? 'PASOKAN RESTORAN & GROSIR',
            'slide5_title'    => $request->slide5_title ?? 'Pilihan Utama 50+ Rumah Makan & Hotel Jawa Barat',
            'slide5_desc'     => $request->slide5_desc ?? 'Jaminan kontinuitas pasokan harian tanpa putus stok. Dapatkan kontrak pasok rutin dengan harga stabil, penjadwalan panen subuh, dan spesifikasi bobot yang sesuai dengan menu andalan resto Anda.',
            'slide5_btn_text' => $request->slide5_btn_text ?? 'Simulasi Pesanan Restoran',
            'slide5_btn_link' => $request->slide5_btn_link ?? '#kalkulator',

            // Slide 6 Custom Content
            'slide6_tag'      => $request->slide6_tag ?? 'LOGISTIK KHUSUS AIR BER-OKSIGEN',
            'slide6_title'    => $request->slide6_title ?? 'Armada Aerasi Cepat Menjangkau Seluruh Jawa Barat',
            'slide6_desc'     => $request->slide6_desc ?? 'Dilengkapi box aerasi motor untuk pengiriman cepat lokal Priangan Timur, pickup tangki drum aerasi aktif, hingga truk tangki oksigen murni untuk partai tonase ke Bandung Raya dan Jabodetabek.',
            'slide6_btn_text' => $request->slide6_btn_text ?? 'Konsultasi Rute & Jadwal Armada',
            'slide6_btn_link' => $request->slide6_btn_link ?? 'wa',
        ];

        // Handle upload banner hero slide 1
        if ($request->hasFile('hero_image')) {
            $oldImage = $currentSettings['hero_image'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image'] = $request->file('hero_image')->store('landing', 'public');
        } elseif ($request->boolean('reset_hero_image')) {
            $oldImage = $currentSettings['hero_image'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image'] = null;
        }

        // Handle upload banner hero slide 2
        if ($request->hasFile('hero_image_2')) {
            $oldImage = $currentSettings['hero_image_2'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_2'] = $request->file('hero_image_2')->store('landing', 'public');
        } elseif ($request->boolean('reset_hero_image_2')) {
            $oldImage = $currentSettings['hero_image_2'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_2'] = null;
        }

        // Handle upload banner hero slide 3
        if ($request->hasFile('hero_image_3')) {
            $oldImage = $currentSettings['hero_image_3'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_3'] = $request->file('hero_image_3')->store('landing', 'public');
        } elseif ($request->boolean('reset_hero_image_3')) {
            $oldImage = $currentSettings['hero_image_3'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_3'] = null;
        }

        // Handle upload banner hero slide 4
        if ($request->hasFile('hero_image_4')) {
            $oldImage = $currentSettings['hero_image_4'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_4'] = $request->file('hero_image_4')->store('landing', 'public');
        } elseif ($request->boolean('reset_hero_image_4')) {
            $oldImage = $currentSettings['hero_image_4'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_4'] = null;
        }

        // Handle upload banner hero slide 5
        if ($request->hasFile('hero_image_5')) {
            $oldImage = $currentSettings['hero_image_5'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_5'] = $request->file('hero_image_5')->store('landing', 'public');
        } elseif ($request->boolean('reset_hero_image_5')) {
            $oldImage = $currentSettings['hero_image_5'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_5'] = null;
        }

        // Handle upload banner hero slide 6
        if ($request->hasFile('hero_image_6')) {
            $oldImage = $currentSettings['hero_image_6'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_6'] = $request->file('hero_image_6')->store('landing', 'public');
        } elseif ($request->boolean('reset_hero_image_6')) {
            $oldImage = $currentSettings['hero_image_6'] ?? null;
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $dataToSave['hero_image_6'] = null;
        }

        LandingSetting::saveSettings($dataToSave);

        return redirect()->route('landing-setting')->with('status', 'Tema & Konten Landing Page berhasil diperbarui!');
    }
}
