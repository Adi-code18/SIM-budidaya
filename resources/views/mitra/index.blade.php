@extends('layouts.app')

@section('title', 'Manajemen Mitra & User - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6">

    <!-- Header Title & Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Mitra & Pengguna</h1>
            <p class="text-sm text-slate-500 mt-1">Direktori pengguna sistem, penanggung jawab pembudidaya, dan mitra distributor.</p>
        </div>
        <button class="px-5 py-2.5 rounded-xl bg-navy-800 hover:bg-navy-900 text-white font-bold text-sm shadow-md transition-all flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah User / Mitra Baru</span>
        </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" 
                   placeholder="Cari nama mitra, email, role, atau nomor HP..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
        </div>
        <div class="flex items-center gap-3">
            <select class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                <option>Semua Role</option>
                <option selected>Manajer</option>
                <option>Pembudidaya</option>
                <option>Mitra Distributor</option>
                <option>Supplier Pakan</option>
            </select>
            <select class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                <option>Status: Aktif</option>
                <option>Status: Nonaktif</option>
            </select>
        </div>
    </div>

    <!-- User Directory Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Pengguna / Mitra</th>
                        <th class="py-4 px-6">Role & Akses</th>
                        <th class="py-4 px-6">Kontak / Email</th>
                        <th class="py-4 px-6">Kolam / Wilayah</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    
                    <!-- User 1: Manajer Utama -->
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" 
                                     alt="Adi Darmawan" 
                                     class="w-10 h-10 rounded-xl object-cover ring-2 ring-sky-500/30">
                                <div>
                                    <h4 class="font-bold text-slate-900">Adi Darmawan</h4>
                                    <span class="text-xs text-slate-400">ID: MNJ-001</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-sky-100 text-sky-800 border border-sky-200">
                                <i class="fa-solid fa-user-shield text-[10px]"></i> Manajer Operasional
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <p class="text-slate-800 font-semibold">manajer@simbudidaya.id</p>
                            <p class="text-xs text-slate-500">+62 812-3456-7890</p>
                        </td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">Pusat Operations (All Sectors)</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-1">
                            <button class="p-2 text-slate-500 hover:text-sky-600 transition-colors" title="Edit User"><i class="fa-solid fa-user-pen"></i></button>
                            <button class="p-2 text-slate-500 hover:text-rose-600 transition-colors" title="Nonaktifkan"><i class="fa-solid fa-user-xmark"></i></button>
                        </td>
                    </tr>

                    <!-- User 2: Pembudidaya -->
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                                    BS
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Budi Santoso</h4>
                                    <span class="text-xs text-slate-400">ID: PBD-014</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fa-solid fa-water text-[10px]"></i> Pembudidaya
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <p class="text-slate-800 font-semibold">budi.santoso@gmail.com</p>
                            <p class="text-xs text-slate-500">+62 857-1122-3344</p>
                        </td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">KOLAM-NW-01 (Magelang)</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-1">
                            <button class="p-2 text-slate-500 hover:text-sky-600 transition-colors" title="Edit User"><i class="fa-solid fa-user-pen"></i></button>
                            <button class="p-2 text-slate-500 hover:text-rose-600 transition-colors" title="Nonaktifkan"><i class="fa-solid fa-user-xmark"></i></button>
                        </td>
                    </tr>

                    <!-- User 3: Mitra Distributor -->
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                                    RS
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">PT Resto Seafood</h4>
                                    <span class="text-xs text-slate-400">ID: MTR-088</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                <i class="fa-solid fa-handshake text-[10px]"></i> Mitra Distributor
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <p class="text-slate-800 font-semibold">procurement@restoseafood.co.id</p>
                            <p class="text-xs text-slate-500">+62 811-9876-5432</p>
                        </td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">Semarang & D.I. Yogyakarta</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right space-x-1">
                            <button class="p-2 text-slate-500 hover:text-sky-600 transition-colors" title="Edit User"><i class="fa-solid fa-user-pen"></i></button>
                            <button class="p-2 text-slate-500 hover:text-rose-600 transition-colors" title="Nonaktifkan"><i class="fa-solid fa-user-xmark"></i></button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
