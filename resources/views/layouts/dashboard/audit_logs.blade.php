@extends('layouts.app')

@section('title', 'Audit Log Aktivitas Sistem - AMS BUDIDAYA')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Audit Log Aktivitas Sistem</h1>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Rekam jejak komprehensif tindakan kritis pengguna (keamanan &amp; akuntabilitas operasional).</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-sky-50 text-sky-800 text-xs font-bold border border-sky-200/80 flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-sky-600"></i>
                <span>Total Terdata: {{ $logs->total() }} Log</span>
            </span>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <h3 class="text-sm font-extrabold text-slate-900">Jejak Aktivitas Krusial (Real-Time Trail)</h3>
            </div>
            <span class="text-[11px] text-slate-400 font-medium">Auto-recorded by System Guards</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/60 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 w-36">Waktu Kejadian</th>
                        <th class="py-3 px-4 w-44">Pengguna</th>
                        <th class="py-3 px-4 w-32">Kategori</th>
                        <th class="py-3 px-4 w-36">Aksi</th>
                        <th class="py-3 px-4">Deskripsi Aktivitas</th>
                        <th class="py-3 px-4 w-28 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($logs as $idx => $log)
                    @php
                        $badgeBg = 'bg-slate-100 text-slate-700';
                        if ($log->kategori === 'pembesaran') $badgeBg = 'bg-emerald-100 text-emerald-800 border border-emerald-200';
                        elseif ($log->kategori === 'pembibitan') $badgeBg = 'bg-teal-100 text-teal-800 border border-teal-200';
                        elseif ($log->kategori === 'pakan') $badgeBg = 'bg-amber-100 text-amber-800 border border-amber-200';
                        elseif ($log->kategori === 'distribusi') $badgeBg = 'bg-sky-100 text-sky-800 border border-sky-200';
                        elseif ($log->kategori === 'keuangan') $badgeBg = 'bg-indigo-100 text-indigo-800 border border-indigo-200';

                        $actionColor = 'text-slate-700';
                        if (str_contains($log->action, 'hapus') || str_contains($log->action, 'void') || str_contains($log->action, 'gagal')) {
                            $actionColor = 'text-rose-600 font-black';
                        } elseif (str_contains($log->action, 'tambah') || str_contains($log->action, 'restock')) {
                            $actionColor = 'text-emerald-700 font-extrabold';
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3 px-4 text-center font-bold text-slate-400">
                            {{ $logs->firstItem() + $idx }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                            <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}</div>
                            <div class="text-[10px] text-slate-400 font-medium">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} WIB</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-extrabold text-[#0B2570]">{{ $log->nama_user }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ $log->role }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $badgeBg }}">
                                {{ $log->kategori }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-[11px] {{ $actionColor }}">
                                {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-700 font-medium">
                            {{ $log->deskripsi }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-[11px] text-slate-500">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-shield-cat text-4xl text-slate-300 mb-2 block"></i>
                            Belum ada catatan aktivitas di log audit sistem.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
