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
| **Master Ikan & Kolam** | CRUD Penuh | Lihat Kolam | Lihat Kolam | Lihat |
| **Stok & Pembelian Pakan** | CRUD Penuh | - | Lihat Saldo | - |
| **Batch Pembibitan (Hatchery)** | CRUD & Approval | CRUD Penuh | Lihat | - |
| **Batch Pembesaran (Grow-out)** | Monitor & Evaluasi | Lihat | CRUD Penuh | Lihat Kesiapan |
| **Logging Pakan & Auto-Deduct** | Monitor & Laporan | Input Log Bibit | Input Log Pakan | - |
| **Panen & Pemberokan** | Monitor Alokasi | - | Input Siap Panen | Update Timer & Sortir |
| **Distribusi & Surat Jalan** | Buat Order & Monitor | - | Lihat | Update Status & Bukti |
| **Keuangan Ledger** | CRUD Penuh | - | - | - |
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
- [x] **Master Data & Inventori**:
  - CRUD Master Ikan (parameter biologis, FCR target, durasi panen, harga jual).
  - CRUD Master Kolam (tipe, kapasitas, penanggung jawab, monitoring pH air).
  - CRUD Master Pakan & Pembelian Restock (auto-replicate ke modul keuangan).
  - CRUD Master Mitra Distributor (integrasi koordinat Leaflet JS & OpenStreetMap).
- [x] **Manajemen Siklus Pembesaran & Pakan**:
  - Integrasi asal bibit (pembibitan sendiri vs beli luar).
  - Logging pakan harian kombinasi pelet dan dedaunan.
  - Pengurangan stok gudang otomatis secara atomik (*DB Transaction Locking*).
  - Kalkulasi Feed Conversion Ratio (FCR) & benchmark efisiensi.
- [x] **Modul Keuangan (Ledger Sederhana)**:
  - Pencatatan otomatis pemasukan (penjualan) dan pengeluaran (pakan & bibit).
- [x] **Mobile Web Interface**:
  - Antarmuka mobile untuk petugas pembibitan, pembesaran, dan distribusi.
  - Upload foto bukti sampai distribusi ikan.

---

### 🔥 Fitur Roadmap / Yang Harus "Digas" (To-Do List)

1. **📊 Engine Validasi Kalkulasi FCR Presisi & Analisis Kerugian Multi-Metode (Prioritas Utama)**
   - [ ] **Kalkulasi 4 Jenis FCR Terpadu di UI Dashboard & Detail Kolam**:
     - **FCR Komersial (Saat Panen):** $\frac{\text{Total Pakan (kg)}}{\text{Biomassa Panen (kg)} - \text{Biomassa Awal (kg)}}$.
     - **FCR Biologis (Panen + Mortalitas):** $\frac{\text{Total Pakan (kg)}}{(\text{Biomassa Panen} + \text{Bobot Ikan Mati}) - \text{Biomassa Awal}}$.
     - **FCR Periodik / Sampling:** $\frac{\text{Pakan Periode Ini (kg)}}{\text{Biomassa Akhir Periode} - \text{Biomassa Awal Periode}}$.
     - **FCR Kumulatif (Running FCR Harian):** $\frac{\text{Akumulasi Pakan s.d. Hari-N}}{\text{Estimasi Biomassa Hari-N} - \text{Biomassa Awal}}$.
   - [ ] **Indikator & Warning Inefisiensi Pakan Berjalan (Biaya Boros)**:
     - Deteksi otomatis jika FCR berjalan > `fcr_max` (dari Master Data Ikan).
     - Menampilkan estimasi nominal rupiah pakan yang terbuang secara *real-time*.
   - [ ] **Kalkulasi Kerugian Panen & Ikan Mati Tak Terdeteksi (*Uncounted Mortality*)**:
     - Menghitung ** '$\text{Ekor Hilang}$ = $\text{jumlah\_tebar\_ekor} - \text{jumlah\_panen\_ekor}$' **.
     - Menghitung ** '$\text{Laba/Rugi Bersih}$ = $\text{Omset Penjualan} - (\text{Biaya Bibit} + \text{Total Biaya Pakan} + \text{Biaya Operasional})$' **.
   - [ ] **Kalkulator Audit & Simulasi FCR Interaktif (Uji Silang Mandiri)**:
     - Widget interaktif di dashboard agar Manajer/User dapat memasukkan parameter pakan, bobot tebar, dan panen secara manual untuk memverifikasi keakuratan rumus secara langsung tanpa ada kalkulasi yang meleset (*zero miss*).

2. **⏱️ Workflow Pemberokan & Grading (Panen & Distribusi)**
   - [ ] Implementasi **Timer Pemberokan** otomatis (12–24 jam) khusus order bertipe `Rumah Makan`.
   - [ ] Form input **Grading & Sortir Ikan** (pencatatan kelas ukuran / ekor per kg).
   - [ ] Status transisi: `Diproses ➔ Pemberokan ➔ Grading ➔ Packing ➔ Siap Kirim`.

3. **📄 Dokumen & Cetak PDF Surat Jalan**
   - [ ] Generator otomatis Nomor Surat Jalan resmi (`SJ/2026/10/001`).
   - [ ] Tombol cetak / export Surat Jalan dalam format **PDF** untuk kurir/petugas logistik.

4. **🧬 Perhitungan Survival Rate (SR) Pembibitan Otomatis**
   - [ ] Field input `jumlah_telur` dan `jumlah_menetas` pada form batch pembibitan.
   - [ ] Auto-kalkulasi rumus $SR = (\text{Jumlah Menetas} / \text{Total Telur}) \times 100\%$.

5. **📑 Ekspor Laporan Komprehensif (PDF & Excel)**
   - [ ] Ekspor Laporan Keuangan Bulanan, Kartu Kontrol Kolam, & Rekap Distribusi ke format PDF (DomPDF).

6. **🛡️ Audit Log Aktivitas Sistem**
   - [ ] Pencatatan log aktivitas krusial pengguna (hapus batch, restock pakan, void transaksi) ke tabel `audit_logs`.

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
│   │   ├── Manajer/           # Dashboard, Master Ikan, Kolam, Pakan, Mitra, Keuangan
│   │   └── Petugas/           # Mobile Controller Distribusi, Pembibitan, Pembesaran
│   ├── Mail/                  # Mailable Class (SendOtpMail)
│   └── Models/                # Model Eloquent (User, Ikan, Kolam, Batch, dll)
├── database/
│   ├── migrations/            # Struktur tabel database
│   └── seeders/               # Data dummy & inisialisasi akun
├── resources/
│   ├── views/
│   │   ├── auth/              # Halaman Login & OTP
│   │   ├── manajer/           # Antarmuka Dashboard Desktop Manajer
│   │   └── mobile_web_petugas/# Antarmuka Mobile Web Petugas Lapangan
│   └── css/ & js/             # Asset Tailwind CSS & Leaflet Maps script
└── routes/
    ├── web.php                # Rute web portal & mobile web
    └── api.php                # Endpoint API
```

---

## 📄 LISENSI & KONTRIBUTOR
* **Pengembang:** Adi Darmawan (*Software Engineer / Lead Analyst*)
* **Proyek:** SIM-BUDIDAYA - Sistem Manajemen & Rantai Pasok Budidaya Ikan 
