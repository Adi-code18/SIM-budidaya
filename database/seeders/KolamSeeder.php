<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kolam;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class KolamSeeder extends Seeder
{
    /**
     * Seeder Fasilitas Kolam Budidaya AquaFarm.
     * Mengacu pada SOP Doc 2.0 Bab 2 Bagian E (Fasilitas Kolam: Kolam pemijahan,
     * pendederan, pembesaran tanah/beton/terpal ber-aerasi, dan kolam pemberokan).
     * Hak akses & penanggung jawab dialokasikan sesuai matriks peran BRD 2.0 & 7.2.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('kolam')->truncate();
        Schema::enableForeignKeyConstraints();

        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? $manajer;
        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? $manajer;
        $petugasDistribusi = User::where('role', 'petugas_distribusi')->first() ?? $manajer;

        $kolamList = [
            // 1. Fasilitas Pembenihan: Kolam Pemijahan (Hatchery / Indukan & Penetasan Telur)
            [
                'id_user'          => $petugasPembibitan->id_user,
                'nama_kolam'       => 'Kolam Pemijahan B1',
                'tipe_kolam'       => 'Hatchery / Pemijahan',
                'kapasitas'        => 500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.00,
                'biaya_pembuatan'  => 2500000.00,
                'catatan'          => 'Kolam pemijahan berpasangan dengan sistem aerasi lembut & substrat kakaban injuk.',
            ],
            // 2. Fasilitas Pembenihan: Kolam Pendederan (Perawatan Larva & Benih Siap Tebar)
            [
                'id_user'          => $petugasPembibitan->id_user,
                'nama_kolam'       => 'Kolam Pendederan C1',
                'tipe_kolam'       => 'Hatchery / Pendederan',
                'kapasitas'        => 15000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 6.90,
                'biaya_pembuatan'  => 3000000.00,
                'catatan'          => 'Kolam pendederan larva burayak ke fingerling 3-5 cm. Pakan alami cacing sutra & artemia.',
            ],
            // 3. Fasilitas Pembesaran: Kolam Beton A1 (Pemeliharaan Rutin / Berjalan)
            [
                'id_user'          => $petugasPembesaran->id_user,
                'nama_kolam'       => 'Kolam Pembesaran A1',
                'tipe_kolam'       => 'Kolam Pembesaran (Beton)',
                'kapasitas'        => 3000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.20,
                'biaya_pembuatan'  => 5000000.00,
                'catatan'          => 'Kolam pembesaran beton sirkulasi air deras pegunungan Ciamis dengan sistem venturi aerasi.',
            ],
            // 4. Fasilitas Pembesaran: Kolam Beton A2 (Siklus Mendekati Panen / Siap Panen)
            [
                'id_user'          => $petugasPembesaran->id_user,
                'nama_kolam'       => 'Kolam Pembesaran A2',
                'tipe_kolam'       => 'Kolam Pembesaran (Beton)',
                'kapasitas'        => 3000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.30,
                'biaya_pembuatan'  => 5000000.00,
                'catatan'          => 'Kolam pembesaran beton untuk ikan ukuran konsumsi siap panen pasokan restoran & pasar.',
            ],
            // 5. Fasilitas Pembesaran: Kolam Terpal B1 (Bioflok / Intensif)
            [
                'id_user'          => $petugasPembesaran->id_user,
                'nama_kolam'       => 'Kolam Pembesaran B1 (Bioflok)',
                'tipe_kolam'       => 'Kolam Terpal / Bioflok',
                'kapasitas'        => 2500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.10,
                'biaya_pembuatan'  => 3500000.00,
                'catatan'          => 'Kolam terpal bundar bioflok diameter 3m dengan blower aerasi 24 jam.',
            ],
            // 6. Fasilitas Pasca Panen: Kolam Buffer & Pemberokan (Karantina Pemuasaan 12-24 Jam)
            [
                'id_user'          => $petugasDistribusi->id_user,
                'nama_kolam'       => 'Kolam Stok & Pemberokan D1',
                'tipe_kolam'       => 'Kolam Penampungan Stok / Pemberokan',
                'kapasitas'        => 2000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.20,
                'biaya_pembuatan'  => 3500000.00,
                'catatan'          => 'Kolam karantina puasa (pemberokan 12-24 jam) air deras untuk membersihkan isi perut dan menghilangkan bau lumpur (Zero Geosmin).',
            ],
        ];

        foreach ($kolamList as $kolam) {
            Kolam::create($kolam);
        }
    }
}
