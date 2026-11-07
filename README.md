# 🐟 SIM-BUDIDAYA (Versi 3.0)
> **Sistem Informasi Manajemen Budidaya & Distribusi Ikan  (End-to-End Supply Chain & Aquaculture Management System)**
>
> *Berdasarkan Acuan Dokumen SOP Peternakan AquaFarm (Doc 2.0) & Business Requirement Document (BRD-SIMBUD-2026-V3.0)*

SIM-BUDIDAYA adalah platform manajemen akuakultur modern yang mengintegrasikan seluruh siklus hidup budidaya perikanan air tawar—mulai dari pemijahan/pembibitan (*hatchery*), pembesaran (*grow-out*), formulasi pakan campuran (pelet + dedaunan organik), analisis biologis (FCR & SR), pengawasan kualitas air (pH & suhu), pemanenan terstandar (pemberokan 12–24 jam), hingga logistik distribusi pesanan berbasis geolokasi (*Leaflet JS & OpenStreetMap*).

---

## 📋 STANDAR OPERASIONAL PROSEDUR (SOP) BUDIDAYA & DISTRIBUSI

Sistem ini dibangun dengan aturan bisnis (*business rules*) yang terikat langsung pada Standar Operasional Prosedur (SOP) resmi perikanan:

### 1. 🐣 SOP Fase Pembibitan & Pembenihan (Hatchery)
* **Tahap 1: Penetasan Telur**
  * Durasi Standar: 1–2 hari (24–48 jam) | Toleransi Maksimal: 3 hari.
  * Ambang Batas Kegagalan: Dinyatakan gagal jika mortalitas telur > 40–50%.
* **Tahap 2: Perawatan Larva (Kuning Telur Habis)**
  * Durasi Standar: 5–7 hari | Toleransi Maksimal: 9 hari.
  * Pakan Alami: Cacing Sutra / Artemia.
  * Ambang Batas Kegagalan: Dinyatakan gagal jika mortalitas larva > 40–60%.
* **Tahap 3: Pendederan I (Benih Siap Tebar 2–3 cm & 3–5 cm)**
  * Durasi Standar: 14–21 hari (2–3 minggu) | Toleransi Maksimal: 25 hari.
  * Ambang Batas Mortalitas Maksimal: 20%.
  * **Target Mutu Baku:** Survival Rate (SR) Larva > 75%, bebas parasit, ukuran seragam.
  * **Rumus Survival Rate (SR):**
    $$\text{SR} = \left(\frac{\text{Jumlah Menetas / Hidup}}{\text{Total Telur Awal}}\right) \times 100\%$$

---

### 2. 🌿 SOP Pembesaran & Manajemen Pakan Campuran (Pakan Mix)
Menggunakan metode kombinasi antara pelet komersial berprotein tinggi dengan pakan alternatif segar/fermentasi (daun talas, kangkung, daun pepaya, azola) guna menekan biaya pakan dan mencapai target efisiensi **FCR < 1.2**.

#### 📊 4 Metode Perhitungan FCR (Feed Conversion Ratio)
1. **FCR Komersial** *(Dihitung saat panen akhir)*:
   $$\text{FCR}_{\text{Komersial}} = \frac{\text{Total Pakan yang Diberikan (kg)}}{\text{Biomassa Panen (kg)} - \text{Biomassa Awal Tebar (kg)}}$$
2. **FCR Biologis** *(Memperhitungkan estimasi ikan mati)*:
   $$\text{FCR}_{\text{Biologis}} = \frac{\text{Total Pakan yang Diberikan (kg)}}{(\text{Biomassa Panen} + \text{Total Estimasi Bobot Ikan Mati}) - \text{Biomassa Awal Tebar}}$$
3. **FCR Periodik / Sampling** *(Evaluasi berkala antar sampling)*:
   $$\text{FCR}_{\text{Periodik}} = \frac{\text{Total Pakan Selama Periode Ini (kg)}}{\text{Biomassa Akhir Periode (kg)} - \text{Biomassa Awal Periode (kg)}}$$
4. **FCR Kumulatif (Running FCR)**:
   $$\text{FCR}_{\text{Kumulatif}} = \frac{\text{Akumulasi Pakan Hari ke-1 s.d. N (kg)}}{\text{Estimasi Biomassa Hari ke-N (kg)} - \text{Biomassa Awal Tebar (kg)}}$$

#### 🐟 Tabel Acuan Standar Budidaya per Spesies Ikan

| Jenis Ikan | Ukuran Konsumsi | Estimasi Panen | Target FCR Ideal | Jenis Pakan yang Didukung |
| :--- | :---: | :---: | :---: | :--- |
| **Lele** | 8–10 ekor / kg | 2.5 – 3 Bulan | **1.0 – 1.2** | Pelet + Vitamin |
| **Bawal** | 3–5 ekor / kg | 3.5 – 4 Bulan | **1.2 – 1.3** | Pelet + Vitamin |
| **Ikan Mas** | 3–4 ekor / kg | 4.5 – 5.5 Bulan | **1.2 – 1.5** | Pelet + Vitamin + Azola |
| **Tawes** | 4–6 ekor / kg | 4.5 – 5.5 Bulan | **1.3 – 1.6** | Pelet + Vitamin + Dedaunan |
| **Patin** | 2–3 ekor / kg | 5.0 – 6.0 Bulan | **1.2 – 1.4** | Pelet + Vitamin |
| **Nila** | 3–5 ekor / kg | 5.0 – 6.0 Bulan | **1.2 – 1.4** | Pelet + Vitamin + Dedaunan |
| **Nilem** | 8–12 ekor / kg | 6.0 – 7.0 Bulan | **1.4 – 1.6** | Pelet + Vitamin + Dedaunan |
| **Gurami** | 2–3 ekor / kg | 10.0 – 12.0 Bulan | **1.5 – 1.8** | Pelet + Vitamin + Dedaunan |

---

### 3. ⏱️ SOP Panen & Pemuasaan (Pemberokan)
* **Pemberokan (Pemuasaan Ikan):** Wajib puasa total **12–24 jam sebelum panen** untuk mengosongkan kotoran perut dan menghilangkan bau lumpur (khususnya untuk pesanan Rumah Makan/Restoran).
* **Jadwal Operasional Panen:**
  * **05.00 – 08.00 WIB:** Waktu panen ideal pagi (suhu sejuk, air dingin, stres ikan minimal).
  * **16.00 – 18.00 WIB:** Waktu panen ideal sore (alternatif jika jadwal pagi terlewat).
  * **Maksimal Pukul 09.00 WIB:** Batas toleransi panas (panen wajib dihentikan jika melewati jam ini).
* **Grading / Sortir:** Dilakukan pemisahan ukuran dan penimbangan per kategori pesanan (pasar tradisional vs resto).

---

### 4. 🚚 SOP Pengiriman, Distribusi & Kebijakan Komersial
* **Sistem Penyerahan DDP (Delivery Duty Paid):** Risiko kematian/kerusakan ikan selama perjalanan ditanggung sepenuhnya oleh peternakan hingga serah terima resmi di lokasi mitra.
* **Standar Mortalitas Transportasi:** Tingkat kematian selama perjalanan harus **< 2%**.
* **Kemasan Distribusi:**
  * Pengiriman Hidup: Kantong plastik beroksigen murni tebal atau drum/tangki dengan aerator aktif.
  * Pengiriman Jarak Jauh / Segar: Packing sterofoam dengan es dingin.
* **Syarat Keuangan:** Sistem pembayaran dengan Uang Muka (**Down Payment 30%–50%**) atau SKBDN untuk transaksi partai besar.

---

### 5. 📦 SOP Pengelolaan Gudang, Saprotan & Aset Peralatan Tambak
* **Katalog Saprotan (Sarana Produksi Tambak / Habis Pakai):**
  * Bahan esensial operasional meliputi kapur dolomit, garam krosok, probiotik bioflok (EM4/Lacto), vitamin C & asam amino, desinfektan kolam, dan antibiotik/obat ikan.
  * Menggunakan sistem peringatan dini ambang batas minimum stok (*Threshold Alert*) untuk mencegah kekosongan bahan kritis saat budidaya berjalan.
* **Katalog Peralatan Tambak (Capital Asset / Reusable):**
  * Aset fisik operasional tambak (pompa celup, aerator turbo/blower, DO meter digital, pH meter, jaring panen, dan timbangan digital).
  * Pelacakan berkala status kondisi fisik aset (*Baik/Normal*, *Perlu Servis*, *Rusak Total*).
* **Atribusi Biaya Kolam (True HPP Calculation):**
  * Setiap pengeluaran saprotan wajib mencatat kolam tujuan (`id_kolam`).
  * Menghitung **Harga Pokok Produksi (HPP) Kolam** secara komprehensif tanpa ada biaya tersembunyi (*zero hidden costs*):
    $$\text{HPP Kolam} = \text{Biaya Benih} + \text{Biaya Pakan} + \text{Biaya Saprotan (Kapur, Probiotik, Vitamin, Obat)}$$
* **Sinkronisasi Otomatis Arus Kas Masuk Gudang:**
  * Setiap transaksi restok/pembelian barang masuk langsung terdebit secara otomatis di buku kas Keuangan (`kategori: 'Pengadaan Saprotan'` atau `'Peralatan Tambak'`). Menghapus risiko pengeluaran operasional yang "gaib" atau tidak tercatat di laporan laba rugi.

---

## 🏗️ TEKNOLOGI & ARSITEKTUR SISTEM

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | **Laravel 13** (PHP 8.3 / 8.4) | Routing, Controller MVC, Eloquent ORM, Database Locking |
| **Database** | **MySQL 8.x** (Laragon) | Skema relasional ACID-compliant |
| **Frontend Styling** | **Tailwind CSS v4** + Blade | Modern glassmorphism UI, responsif desktop & mobile |
| **Peta & Geolokasi** | **Leaflet JS & OpenStreetMap (OSM)** | Pemetaan koordinat kolam & tracking rute mitra distributor |
| **Autentikasi & 2FA** | **Multi-Factor Auth** | • **Email OTP** (SMTP Gmail) untuk Manajer<br>• **Google Authenticator (TOTP)** (`pragmarx/google2fa`) untuk Petugas |
| **Anti-Bot Security** | **Cloudflare Turnstile** | Proteksi keamanan login & form OTP |
| **Mobile Web PWA** | **Laravel PWA** (`silviolleite/laravelpwa`) | Mobile interface untuk petugas pembibitan, kolam, dan kurir |

---

## 👥 MATRIKS PERAN & HAK AKSES PENGGUNA (RBAC)

| Modul / Fitur | 👑 Manajer | 🌱 Petugas Pembibitan | 🌿 Petugas Pembesaran | 🚚 Petugas Distribusi |
| :--- | :---: | :---: | :---: | :---: |
| **Autentikasi & 2FA** | Full (Email OTP) | Akun Sendiri (TOTP) | Akun Sendiri (TOTP) | Akun Sendiri (TOTP) |
| **Master Ikan & Jenis** | CRUD Penuh | - | - | - |
| **Manajemen Kolam (Capex)** | CRUD Penuh | Lihat Kolam | Lihat Kolam | Lihat |
| **Stok & Pembelian Pakan** | CRUD Penuh | - | Lihat Saldo | - |
| **Inventaris & Gudang (Saprotan & Alat)** | CRUD & Mutasi | - | Lihat & Pakai | - |
| **Batch Pembibitan (Hatchery)** | CRUD & Approval | CRUD Penuh | Lihat | - |
| **Batch Pembesaran (Grow-out)** | Monitor & Evaluasi | Lihat | CRUD Penuh | Lihat Kesiapan |
| **Logging Pakan & Auto-Deduct** | Monitor & Laporan | Input Log Bibit | Input Log Pakan | - |
| **Panen & Pemberokan** | Monitor Alokasi | - | Input Siap Panen | Update Timer & Sortir |
| **Distribusi & Termin Piutang** | Buat Order, Plafon & Bayar Piutang | - | Lihat | Update Status & Bukti |
| **Keuangan, Neraca & Depresiasi** | CRUD Penuh, Neraca & Depresiasi | - | - | - |
| **Peta Interaktif (GIS)** | Peta Global | - | - | Rute Kirim Mitra |

---

## 📊 STATUS IMPLEMENTASI FITUR

### ✅ Fitur yang Sudah Selesai (Completed)
- [x] **Autentikasi Aman & 2FA**:
  - Login multi-role dengan proteksi password bcrypt.
  - 2FA Email OTP untuk Manajer via SMTP Gmail.
  - 2FA Google Authenticator (TOTP QR Code) untuk Petugas.
  - Lupa Password via Email OTP token.
  - Session Locker (`last_session_id`) mencegah login ganda.
- [x] **Master Data, Kolam & Inventori**:
  - CRUD Master Ikan (parameter biologis, FCR target, durasi panen, harga jual).
  - CRUD Master Kolam mandiri di sidebar (tipe, kapasitas, penanggung jawab, monitoring pH).
  - Integrasi otomatis biaya pembuatan kolam baru ke pengeluaran kas Keuangan (`Pembangunan Kolam`).
  - CRUD Master Pakan & Pembelian Restock (auto-replicate ke modul keuangan).
  - CRUD Master Mitra Distributor (integrasi koordinat Leaflet JS & OpenStreetMap).
- [x] **Inventaris & Gudang Operasional (`/gudang`)**:
  - Katalog barang terbagi 2 kategori: Saprotan (habis pakai) dan Peralatan Tambak (aset fisik).
  - Penomoran kode unik otomatis: `SPT-XXXX` (Saprotan) & `ALT-XXXX` (Peralatan).
  - Sinkronisasi otomatis transaksi restok ke buku kas Keuangan (mencegah biaya siluman).
  - Atribusi pemakaian barang langsung ke kolam tujuan guna kalkulasi HPP riil.
  - Indikator peringatan stok kritis (*minimum threshold warning*) & status kondisi alat (*Baik, Perlu Servis, Rusak*).
  - Rekam jejak audit trail seluruh mutasi gudang (`masuk_pembelian`, `keluar_pemakaian_kolam`, `penyesuaian_rusak`).
- [x] **Manajemen Siklus Pembesaran & Pakan**:
  - Integrasi asal bibit (pembibitan sendiri vs beli luar).
  - Logging pakan harian kombinasi pelet dan dedaunan.
  - Pengurangan stok gudang otomatis secara atomik (*DB Transaction Locking*).
  - Kalkulasi Feed Conversion Ratio (FCR) & benchmark efisiensi.
- [x] **Workflow Distribusi, Piutang Mitra & Proteksi Klaim Susut DDP**:
  - Kebijakan Pre-Order masa depan (H+1 s.d. H+14): ikan tetap berada di kolam asal dan diberi makan normal hingga H-1 keberangkatan.
  - Pilihan panen parsial vs kuras total ke kolam penampungan/buffer.
  - Proteksi *feeding lock* (larangan pemberian pakan) otomatis saat ikan berstatus masa puasa / pemberokan.
  - Pencatatan susut bobot & mortalitas tiba di lokasi serah terima (DDP toleransi $\le 2\%$).
  - Sistem Termin Pembayaran Piutang (*Credit Terms* 7 s.d. 30 hari) & batas plafon kredit per distributor.
  - Otomasi potongan klaim susut timbangan DDP melebihi toleransi dengan pencatatan Cadangan Klaim di sisi Kewajiban Neraca.
  - Modal Catat Pembayaran Piutang (cicilan/lunas) yang otomatis memposting mutasi kas masuk ke Buku Kas Keuangan.
- [x] **Modul Keuangan (Executive Balance Sheet & Cash Ledger Terkalibrasi)**:
  - Pencatatan otomatis pemasukan (penjualan panen, setoran modal disetor, pelunasan piutang) dan pengeluaran (pakan, bibit, saprotan gudang, aset alat tambak, dan konstruksi kolam).
  - **Executive Balance Sheet (Neraca Posisi Keuangan)**: Aktiva Lancar + Tetap Bersih vs Kewajiban + Ekuitas Modal & Laba Ditahan (100% seimbang secara matematis).
  - **Akumulasi Penyusutan Aset Tetap Metode Garis Lurus (*Straight-Line*)**: Depresiasi Kolam (48 bulan) dan Mesin/Alat (36 bulan) sebagai Kontra-Aktiva pengurang Nilai Perolehan ke Nilai Buku Bersih (*Net Book Value*).
  - **Kalibrasi Biaya Pokok Produksi (HPP) & Persediaan Biologis**: Pemisahan belanja modal (*Capex*) dari biaya operasional riil, menghasilkan HPP per Kg standar industri (~Rp 21.500/kg) dan valuasi persediaan ikan hidup yang presisi.
  - **Buku Kas & Cash Ledger Paginasi Lembar (Anti-Lag UX)**: Pembagian data per lembar (10, 15, 25, 50, Semua baris) dengan navigasi instan dan tampilan bebas potong.
- [x] **Mobile Web Interface**:
  - Antarmuka mobile untuk petugas pembibitan, pembesaran, dan distribusi.
  - Upload foto bukti sampai distribusi ikan.

---

### 🔥 Fitur Unggulan Sistem (Completed Highlights)

1. **📊 Engine Validasi Kalkulasi FCR Presisi & Analisis Kerugian Multi-Metode**
   - [x] **Kalkulasi 4 Jenis FCR Terpadu di UI Dashboard & Detail Kolam**:
     - **FCR Komersial (Saat Panen):** $\frac{\text{Total Pakan (kg)}}{\text{Biomassa Panen (kg)} - \text{Biomassa Awal (kg)}}$.
     - **FCR Biologis (Panen + Mortalitas):** $\frac{\text{Total Pakan (kg)}}{(\text{Biomassa Panen} + \text{Bobot Ikan Mati}) - \text{Biomassa Awal}}$.
     - **FCR Periodik / Sampling:** $\frac{\text{Pakan Periode Ini (kg)}}{\text{Biomassa Akhir Periode} - \text{Biomassa Awal Periode}}$.
     - **FCR Kumulatif (Running FCR Harian):** $\frac{\text{Akumulasi Pakan s.d. Hari-N}}{\text{Estimasi Biomassa Hari-N} - \text{Biomassa Awal}}$.
   - [x] **Indikator & Warning Inefisiensi Pakan Berjalan (Biaya Boros)**:
     - Deteksi otomatis jika FCR berjalan > `fcr_max` (dari Master Data Ikan).
     - Menampilkan estimasi nominal rupiah pakan yang terbuang secara *real-time*.
   - [x] **Kalkulasi Kerugian Panen & Ikan Mati Tak Terdeteksi (*Uncounted Mortality*)**:
     - Menghitung  $$\text{Ekor Hilang} = \text{Jumlah Tebar Ekor} - \text{Jumlah Panen Ekor}$$
     - Menghitung  $\text{Laba/Rugi Bersih}$ = $\text{Omset Penjualan} - (\text{Biaya Bibit} + \text{Total Biaya Pakan} + \text{Biaya Operasional})$ .
   - [x] **Kalkulator Audit & Simulasi FCR Interaktif (Uji Silang Mandiri)**:
     - Widget interaktif di dashboard agar Manajer/User dapat memasukkan parameter pakan, bobot tebar, dan panen secara manual untuk memverifikasi keakuratan rumus secara langsung tanpa ada kalkulasi yang meleset (*zero miss*).

2. **⏱️ Workflow Pemberokan & Grading (Panen & Distribusi)**
   - [x] Implementasi **Timer Pemberokan** otomatis (12–24 jam) khusus order bertipe `Rumah Makan` / mitra resto.
   - [x] Form input **Grading & Sortir Ikan** (pencatatan kelas ukuran / ekor per kg).
   - [x] Status transisi: `Diproses ➔ Pemberokan ➔ Grading ➔ Packing ➔ Siap Kirim`.

3. **📄 Dokumen & Cetak PDF Surat Jalan**
   - [x] Generator otomatis Nomor Surat Jalan resmi (`SJ/2026/10/001`).
   - [x] Tombol cetak / export Surat Jalan dalam format **PDF** untuk kurir/petugas logistik via DomPDF.

4. **🧬 Perhitungan Survival Rate (SR) Pembibitan Otomatis**
   - [x] Field input `jumlah_telur` dan `jumlah_menetas` pada form batch pembibitan.
   - [x] Auto-kalkulasi rumus $SR = (\text{Jumlah Menetas} / \text{Total Telur}) \times 100\%$.

5. **📑 Ekspor Laporan Komprehensif (PDF & Excel)**
   - [x] Ekspor Laporan Keuangan Bulanan, Kartu Kontrol Kolam, & Rekap Distribusi ke format PDF (DomPDF).

6. **🛡️ Audit Log Aktivitas Sistem**
   - [x] Pencatatan log aktivitas krusial pengguna (hapus batch, restock pakan, void transaksi) ke tabel `audit_logs` & antarmuka riwayat audit log.

7. **📦 Inventaris & Gudang Operasional Terintegrasi Keuangan**
   - [x] Dashboard KPI Nilai Aset Gudang (Total Nilai Stok, Nilai Saprotan, Nilai Peralatan, Stok Kritis).
   - [x] Form restok barang otomatis memposting mutasi kas pengeluaran ke buku besar Keuangan.
   - [x] Atribusi pemakaian saprotan langsung ke kolam untuk menghitung HPP riil tanpa kebocoran.

8. **🌊 Company Landing Page & WhatsApp Sales Hub (`/`)**
   - [x] Antarmuka profil publik modern bertema Aquafarm Priangan untuk menarik pembeli partai besar & calon investor.
   - [x] Katalog komoditas ikan air tawar siap panen (Nila Merah, Lele, Patin, Gurame) dengan spesifikasi ukuran konsumsi.
   - [x] Simulator / Kalkulator Pesanan Cepat dengan integrasi otomatis draft WhatsApp Click-to-Chat.
   - [x] Pemaparan 4 pilar SOP mutu (pemberokan 24 jam di air deras bebas bau lumpur, pakan mix probiotik, garansi DDP, panen dingin).
   - [x] Ruang kemitraan B2B restoran, hotel, plasma kolam, dan kerjasama permodalan ekspansi unit kolam.
   - [x] Floating WhatsApp widget interaktif di pojok kanan bawah.
   - [x] Pemisahan privasi akses: tombol login internal ditiadakan dari navbar publik (portal staf diakses privat via `/login`).

9. **⚖️ Neraca Posisi Keuangan Eksekutif (Aktiva vs Pasiva) & Depresiasi Garis Lurus**
   - [x] **Keseimbangan Presisi (Aktiva = Pasiva, Selisih Rp 0)**:
     - **Aktiva Lancar**: Kas riil likuid operasional, Piutang termin mitra berjalan, Persediaan biologis ikan hidup di kolam aktif, dan Persediaan pakan/saprotan gudang.
     - **Aktiva Tetap Bersih (*Net Book Value*)**: Nilai perolehan kotor kolam & peralatan mesin dikurangi Akumulasi Penyusutan (*Accumulated Depreciation*).
     - **Kewajiban**: Cadangan klaim potongan susut timbangan DDP mitra.
     - **Ekuitas**: Modal disetor awal pemilik/investor + Laba ditahan bersih dari akumulasi operasional.
   - [x] **Akumulasi Penyusutan Metode Garis Lurus (*Straight-Line*)**:
     - Fasilitas Fisik Kolam Budidaya disusutkan merata selama **4 Tahun (48 Bulan)**.
     - Peralatan Tambak, Pompa & Aerasi disusutkan merata selama **3 Tahun (36 Bulan)**.
     - Dihitung dinamis dan otomatis berdasarkan umur operasional tambak sejak transaksi pertama.
   - [x] **Kalibrasi Biaya Produksi & HPP Wajar**:
     - Pemisahan belanja modal (*Capex*) dari biaya operasional harian (*Opex*).
     - HPP per Kg stabil di rentang standar budidaya air tawar (~Rp 21.500/kg) untuk memvalidasi nilai biomassa hidup secara objektif.

10. **🛡️ Manajemen Risiko Distribusi, Termin Piutang & Proteksi Klaim Susut (DDP)**
    - [x] **Kontrol Termin Piutang Mitra (*Credit Terms*)**:
      - Pengaturan batas tempo bayar (Cash On Delivery, 7, 14, hingga 30 hari).
      - Monitoring batas plafon kredit per mitra distributor dengan visual badge status: *Lunas, Tempo, Menunggak*.
      - Widget KPI Risiko Piutang di dashboard Distribusi (Total Piutang Berjalan, Piutang Jatuh Tempo, Cadangan Klaim).
    - [x] **Proteksi Pemotongan Klaim Susut Bobot Timbangan**:
      - Toleransi standar DDP perikanan $\le 2\%$.
      - Otomasi pemotongan nilai tagihan akhir jika susut bobot timbangan di lokasi serah terima melebihi ambang batas toleransi.
      - Alokasi otomatis potongan klaim ke pos kewajiban cadangan klaim neraca keuangan.
    - [x] **Modal Pelunasan Piutang Terintegrasi Buku Kas**:
      - Tombol aksi *"Catat Bayar"* langsung dari baris pesanan distribusi untuk menerima cicilan bertahap maupun pelunasan penuh.
      - Otomatis mencatat jurnal uang masuk ke Buku Kas Keuangan tanpa duplikasi.

11. **📖 Buku Kas Digital & Cash Ledger Paginasi Lembar (Anti-Lag UX)**
    - [x] **Paginasi Lembar Cerdas (Pagination)**:
      - Menghilangkan *infinite scroll* berat saat menangani ribuan transaksi kas.
      - Pilihan ukuran baris: `10`, `15`, `25`, `50`, dan `Semua` entri per lembar.
      - Navigasi halaman lengkap: *Pertama (`«`), Sebelumnya (`‹`), Angka Halaman Dinamis, Selanjutnya (`›`), Terakhir (`»`), serta Input Lompat Halaman Cepat*.
    - [x] **Tampilan Ramping Bebas Potong**:
      - Header toolbar terpadu (Badge jumlah entri kas, pencarian kata kunci instan, filter tipe mutasi debit/kredit, dan selektor baris dalam satu baris horizontal rapat).
      - Pemadatan teks uraian transaksi (`line-clamp-1` + hover tooltip) menjaga tinggi baris tetap ramping (~42px).
      - Kompatibilitas cetak (*Print-Friendly*): secara otomatis membuka seluruh data kas saat tombol *"Cetak Buku Kas"* atau print browser ditekan tanpa terpotong batas halaman.

---

## ⚙️ PANDUAN INSTALASI & MENJALANKAN SISTEM

### 1. Prasyarat Sistem
* **Laragon** (atau XAMPP) dengan PHP 8.3 / 8.4
* **Composer 2.x**
* **Node.js (v20+) & NPM**
* **MySQL 8.0+**

### 2. Langkah Instalasi

```bash
# 1. Salin file environment
copy .env.example .env

# 2. Install dependensi PHP & Node.js
composer install
npm install

# 3. Generate Application Key
php artisan key:generate
```

### 3. Konfigurasi `.env` & SMTP Gmail

Sesuaikan konfigurasi pada file [`.env`](file:///.env):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=budidaya
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi SMTP Gmail untuk Email OTP 2FA
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=sandi_aplikasi_gmail_16_digit
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="email_anda@gmail.com"
MAIL_FROM_NAME="SIM-BUDIDAYA"
MAIL_VERIFY_PEER=false
```

> **Catatan Pengaturan PHP Laragon (SSL Fix):**
> Buka file `php.ini` Laragon (Klik kanan Laragon ➔ **PHP** ➔ **php.ini**), pastikan baris `openssl.cafile` sudah diaktifkan tanpa tanda titik koma:
> ```ini
> openssl.cafile = "C:\laragon\etc\ssl\cacert.pem"
> ```

### 4. Migrasi & Seeder Data

```bash
php artisan migrate:fresh --seed
```

### 5. Menjalankan Aplikasi

```bash
# Terminal 1: Laravel Backend
php artisan serve

# Terminal 2: Vite Hot-Reload
npm run dev
```

Akses aplikasi melalui browser:
* **Web Portal Manajer & Utama:** `http://127.0.0.1:8000`
* **Mobile Web Petugas Distribusi:** `http://127.0.0.1:8000/mobile-petugas/login`
* **Mobile Web Petugas Pembibitan:** `http://127.0.0.1:8000/petugas-pembibitan`
* **Mobile Web Petugas Pembesaran:** `http://127.0.0.1:8000/petugas-pembesaran`

---

## 🔑 AKUN UJI COBA DEFAULT (DEMO)

| Peran (Role) | Email | Password | Metode 2FA |
| :--- | :--- | :--- | :--- |
| **Manajer** | `manager@budidaya.com` | `password` | Email OTP (6-digit) |
| **Petugas Pembibitan** | `bibit@budidaya.com` | `password` | Google Authenticator / TOTP |
| **Petugas Pembesaran** | `besar@budidaya.com` | `password` | Google Authenticator / TOTP |
| **Petugas Distribusi** | `distribusi@budidaya.com` | `password` | Google Authenticator / TOTP |

---

## 📁 STRUKTUR DIREKTORI PROYEK

```text
SIM-budidaya/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/              # Auth, Email OTP, TOTP 2FA, Reset Password
│   │   ├── Manajer/           # Dashboard, Kolam, Gudang, Pakan, Distribusi, Keuangan, Mitra
│   │   └── Petugas/           # Mobile Controller Distribusi, Pembibitan, Pembesaran
│   ├── Mail/                  # Mailable Class (SendOtpMail)
│   └── Models/                # Eloquent: InventarisGudang, LogTransaksiGudang, Kolam, Batch, dll
├── database/
│   ├── migrations/            # Skema tabel database (Gudang, Kolam, Keuangan, dll)
│   └── seeders/               # Seeder data realistis (Inventaris, Kolam, Pakan, Akun)
├── resources/
│   ├── views/
│   │   ├── auth/              # Halaman Login & OTP
│   │   ├── layouts/           # Antarmuka Dashboard Manajer (Gudang, Kolam, Distribusi, Keuangan)
│   │   └── mobile_web_petugas/# Antarmuka Mobile Web Petugas Lapangan
│   └── css/ & js/             # Asset Tailwind CSS, Alpine.js & Leaflet Maps script
└── routes/
    ├── web.php                # Rute web portal & mobile web
    └── api.php                # Endpoint API
```

---

## 📄 LISENSI & KONTRIBUTOR
* **Pengembang:** Adi Darmawan (*Software Engineer / Lead Analyst*)
* **Proyek:** SIM-BUDIDAYA - Sistem Manajemen & Rantai Pasok Budidaya Ikan 
