@extends('layouts.app')

@section('title', 'Log Pakan Harian - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ alertShow: false }">

    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Log Pakan Harian</h1>
            <p class="text-sm text-slate-500 mt-1">Catat dan pantau pemberian pakan harian untuk kontrol FCR yang optimal.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
            <i class="fa-solid fa-circle-check"></i>
            <span>Target FCR Terjaga (1.15)</span>
        </div>
    </div>

    <!-- Alert Notification -->
    <div x-show="alertShow" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="p-4 rounded-xl bg-emerald-500 text-white flex items-center justify-between shadow-lg shadow-emerald-500/20" 
         style="display: none;">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <span class="font-bold text-sm">Log Pakan Harian berhasil disimpan ke sistem!</span>
        </div>
        <button @click="alertShow = false" class="text-white hover:text-emerald-100"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <!-- Main Grid: Form Entry + Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left 7 cols: Form Entry Card -->
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-clip text-sky-600"></i>
                    <span>Input Catatan Pakan Baru</span>
                </h3>
                <p class="text-xs text-slate-500">Lengkapi formulir di bawah sesuai pemberian pakan aktual di lapangan</p>
            </div>

            <form @submit.prevent="alertShow = true; setTimeout(() => alertShow = false, 4000)" class="space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Kolam / Batch</label>
                        <select required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="">-- Pilih Kolam --</option>
                            <option value="KOLAM-NW-01" selected>KOLAM-NW-01 (Ikan Nila Hitam)</option>
                            <option value="KOLAM-SL-04">KOLAM-SL-04 (Ikan Lele Sangkuriang)</option>
                            <option value="KOLAM-KL-02">KOLAM-KL-02 (Ikan Gurame Super)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Waktu Pemberian</label>
                        <select required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="pagi">Pagi (07:00 WIB)</option>
                            <option value="siang" selected>Siang (12:30 WIB)</option>
                            <option value="sore">Sore (17:00 WIB)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jenis / Merek Pakan</label>
                        <select required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="floating">Pelet Apung Hi-Pro-Vite 781</option>
                            <option value="sinking">Pelet Tenggelam Prima Feed</option>
                            <option value="starter">Pelet Benih PF-1000</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jumlah Pakan (Kg)</label>
                        <div class="relative">
                            <input type="number" step="0.5" value="45.0" required class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">KG</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nafsu Makan & Kondisi Air</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-bold bg-emerald-50 border-emerald-300 text-emerald-700">
                            <input type="radio" name="condition" checked class="text-emerald-600 focus:ring-emerald-500">
                            <span>Sangat Baik</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-bold bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100">
                            <input type="radio" name="condition" class="text-sky-600 focus:ring-sky-500">
                            <span>Normal</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-bold bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100">
                            <input type="radio" name="condition" class="text-rose-600 focus:ring-rose-500">
                            <span>Menurun</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea rows="2" placeholder="Contoh: Suhu air 28°C, pH 7.2, respon ikan aktif..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-6 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm shadow-md shadow-sky-600/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Log Pakan Harian</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right 5 cols: Stats & Recommendations -->
        <div class="lg:col-span-5 space-y-5">
            
            <div class="bg-gradient-to-br from-navy-800 to-navy-900 p-6 rounded-2xl text-white shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-300">Ringkasan Hari Ini</span>
                    <i class="fa-solid fa-chart-pie text-sky-400 text-lg"></i>
                </div>
                <div>
                    <span class="text-3xl font-extrabold block">185.5 <span class="text-base font-semibold text-slate-300">kg</span></span>
                    <span class="text-xs text-slate-300">Total Pakan Terdistribusi Hari Ini</span>
                </div>
                <div class="pt-3 border-t border-white/10 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block">Rata-rata per Kolam</span>
                        <span class="font-bold text-white text-sm">46.3 kg/kolam</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Efisiensi FCR</span>
                        <span class="font-bold text-emerald-400 text-sm">1.14 (Sangat Baik)</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    <span>Rekomendasi Pakan Manajer</span>
                </h4>
                <ul class="space-y-2.5 text-xs text-slate-600">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i>
                        <span>Kolam <strong>KOLAM-NW-01</strong> siap untuk peningkatan pakan +5% minggu depan.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                        <span>Pastikan aerasi menyala penuh saat pemberian pakan sore di <strong>KOLAM-KL-02</strong>.</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- Feed Log History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Riwayat Pemberian Pakan Terakhir</h3>
            <span class="text-xs font-semibold text-slate-500">Menampilkan 5 data terbaru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Waktu & Tanggal</th>
                        <th class="py-3.5 px-6">Kolam Target</th>
                        <th class="py-3.5 px-6">Merek Pakan</th>
                        <th class="py-3.5 px-6">Jumlah</th>
                        <th class="py-3.5 px-6">Kondisi Ikan</th>
                        <th class="py-3.5 px-6">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-6 text-xs font-semibold text-slate-900">Hari ini, 12:30 WIB</td>
                        <td class="py-4 px-6 font-bold text-slate-900">KOLAM-NW-01</td>
                        <td class="py-4 px-6">Hi-Pro-Vite 781</td>
                        <td class="py-4 px-6 font-bold text-sky-600">45.0 kg</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Sangat Baik</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500">Budi Santoso</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-6 text-xs font-semibold text-slate-900">Hari ini, 07:00 WIB</td>
                        <td class="py-4 px-6 font-bold text-slate-900">KOLAM-SL-04</td>
                        <td class="py-4 px-6">Prima Feed</td>
                        <td class="py-4 px-6 font-bold text-sky-600">50.0 kg</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Sangat Baik</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500">Ahmad Fauzi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
