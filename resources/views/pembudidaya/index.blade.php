@extends('layouts.app')

@section('title', 'Manajemen Pembudidaya & Kolam - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false, search: '' }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pembudidaya & Kolam</h1>
            <p class="text-sm text-slate-500 mt-1">Pengelolaan data kolam, populasi benih ikan, dan lokasi pembudidaya.</p>
        </div>
        <div>
            <button @click="modalOpen = true" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-700 to-sky-600 hover:from-blue-800 hover:to-sky-700 text-white font-bold text-sm shadow-md shadow-blue-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-circle-plus"></i>
                <span>Tambah Kolam / Pembudidaya</span>
            </button>
        </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-water text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Benih / Ekor</p>
                <h3 class="text-2xl font-extrabold text-slate-900">1,240,500 <span class="text-xs font-medium text-slate-500">ekor</span></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tingkat Keberhasilan</p>
                <h3 class="text-2xl font-extrabold text-slate-900">94.2% <span class="text-xs font-semibold text-emerald-600">High Yield</span></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-hourglass-half text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kolam Siap Panen</p>
                <h3 class="text-2xl font-extrabold text-slate-900">42 <span class="text-xs font-semibold text-amber-600">Kolam</span></h3>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" 
                   x-model="search"
                   placeholder="Cari ID Kolam, Pembudidaya, atau Lokasi..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
        </div>
        <div class="flex items-center gap-3">
            <select class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                <option>Semua Status</option>
                <option>Sehat / Optimal</option>
                <option>Siap Panen</option>
                <option>Perlu Perhatian</option>
            </select>
            <select class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                <option>Semua Komoditas</option>
                <option>Ikan Nila</option>
                <option>Ikan Lele</option>
                <option>Ikan Gurame</option>
            </select>
        </div>
    </div>

    <!-- Kolam Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">ID Kolam & Lokasi</th>
                        <th class="py-4 px-6">Pembudidaya</th>
                        <th class="py-4 px-6">Jenis Ikan</th>
                        <th class="py-4 px-6">Tebar Benih</th>
                        <th class="py-4 px-6">Populasi (Ekor)</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900">KOLAM-NW-01</div>
                            <div class="text-xs text-slate-500">Sektor Barat - Magelang</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-xs">BS</div>
                                <span>Budi Santoso</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Ikan Nila Hitam</td>
                        <td class="py-4 px-6 text-xs text-slate-500">12 Mei 2026</td>
                        <td class="py-4 px-6 font-bold text-slate-900">25,000</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Optimal
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-1">
                            <button class="p-2 text-slate-500 hover:text-sky-600 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="p-2 text-slate-500 hover:text-rose-600 transition-colors"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900">KOLAM-SL-04</div>
                            <div class="text-xs text-slate-500">Sektor Selatan - Solo</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">AF</div>
                                <span>Ahmad Fauzi</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Ikan Lele Sangkuriang</td>
                        <td class="py-4 px-6 text-xs text-slate-500">01 Jun 2026</td>
                        <td class="py-4 px-6 font-bold text-slate-900">40,000</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Siap Panen
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-1">
                            <button class="p-2 text-slate-500 hover:text-sky-600 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="p-2 text-slate-500 hover:text-rose-600 transition-colors"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900">KOLAM-KL-02</div>
                            <div class="text-xs text-slate-500">Sektor Timur - Klaten</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">SR</div>
                                <span>Siti Rahmawati</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Ikan Gurame Super</td>
                        <td class="py-4 px-6 text-xs text-slate-500">10 Mar 2026</td>
                        <td class="py-4 px-6 font-bold text-slate-900">15,000</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Optimal
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-1">
                            <button class="p-2 text-slate-500 hover:text-sky-600 transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="p-2 text-slate-500 hover:text-rose-600 transition-colors"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Kolam -->
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-900">Tambah Kolam Baru</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form @submit.prevent="modalOpen = false" class="space-y-4 text-sm">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Kode / ID Kolam</label>
                    <input type="text" placeholder="Contoh: KOLAM-NW-05" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Jenis Ikan</label>
                        <select class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500">
                            <option>Ikan Nila Hitam</option>
                            <option>Ikan Lele Sangkuriang</option>
                            <option>Ikan Gurame Super</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Populasi Benih (Ekor)</label>
                        <input type="number" placeholder="20000" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Penanggung Jawab / Pembudidaya</label>
                    <input type="text" placeholder="Nama Pembudidaya" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 text-white font-bold hover:bg-sky-700 shadow-md shadow-sky-600/20 transition-all">Simpan Kolam</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
