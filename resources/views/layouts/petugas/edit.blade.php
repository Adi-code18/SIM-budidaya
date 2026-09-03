@extends('layouts.app')

@section('title', 'Edit Data Petugas - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Breadcrumb & Header Page -->
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="{{ route('petugas') }}" class="hover:text-slate-600 transition-colors">Manajemen Petugas</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600">Edit Data Petugas</span>
        </div>
        <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Edit Data Petugas</h1>
        <p class="text-xs text-slate-500 font-medium">Update information for staff Ahmad Rifat</p>
    </div>

    <!-- Edit Form Card Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <!-- Profile Header Avatar Banner -->
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=160" 
                     alt="Ahmad Rifat" 
                     class="w-16 h-16 rounded-full object-cover border-2 border-sky-500/20 shadow-xs">
                <div class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white"></div>
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-lg font-extrabold text-slate-900">Ahmad Rifat</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 uppercase">
                        Aktif
                    </span>
                </div>
                <span class="text-xs text-slate-400 font-medium">Logistik Pasar • ID: BUS-STF-088</span>
            </div>
        </div>

        <!-- Inputs Form -->
        <form action="#" method="POST" @submit.prevent class="space-y-5">
            <div>
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Full Name</label>
                <input type="text" 
                       value="Ahmad Rifat" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Phone Number / WhatsApp</label>
                    <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                        <span class="px-3 py-2.5 text-xs font-bold text-slate-500 bg-slate-100/90 border-r border-slate-200 shrink-0 flex items-center gap-1.5">
                            <span>🇮🇩</span>
                            <span>+62</span>
                        </span>
                        <input type="tel" 
                               value="0812-3456-7890" 
                               placeholder="812-3456-7890 / 081234567890"
                               class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </div>
                        <input type="email" 
                               value="ahmad.rifat@agribisnis-budidaya.co.id" 
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('petugas') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all">
                    Save Changes
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
