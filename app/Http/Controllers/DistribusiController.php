<?php

namespace App\Http\Controllers;

use App\Models\MitraDistributor;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;

class DistribusiController extends Controller
{
    public function index()
    {
        $transaksiRecords = TransaksiDistribusi::with(['mitra', 'batchPembesaran', 'user'])->latest('id_transaksi')->get();
        $mitras = MitraDistributor::all();

        $totalPesanan = TransaksiDistribusi::count();
        $pemberokianCount = TransaksiDistribusi::where('status_order', 'dalam_pengiriman')->count();
        $siapKirimCount = TransaksiDistribusi::where('status_order', 'siap_kirim')->orWhere('status_order', 'pending')->count();
        $selesaiCount = TransaksiDistribusi::where('status_order', 'selesai')->count();

        $kpis = [
            'total'       => $totalPesanan > 0 ? $totalPesanan : 4,
            'pemberokian' => $pemberokianCount > 0 ? $pemberokianCount : 1,
            'siapKirim'   => $siapKirimCount > 0 ? $siapKirimCount : 2,
            'selesai'     => $selesaiCount > 0 ? $selesaiCount : 1,
        ];

        $orders = [];
        foreach ($transaksiRecords as $t) {
            $status = $t->status_order;
            if ($status === 'dalam_pengiriman') {
                $status = 'pemberokian'; // UI key
            }

            $orders[] = [
                'id'       => '#ORD-2023-' . str_pad($t->id_transaksi, 4, '0', STR_PAD_LEFT),
                'customer' => $t->mitra ? $t->mitra->nama_mitra : 'Mitra Retail',
                'volume'   => number_format($t->Total_kg, 0, ',', '.') . ' kg',
                'status'   => $status,
                'alamat'   => $t->mitra ? $t->mitra->alamat : '-',
                'tanggal'  => $t->tanggal_order ? \Carbon\Carbon::parse($t->tanggal_order)->translatedFormat('d/m/y') : '5/8/26',
                'label'    => true
            ];
        }

        return view('layouts.distribusi.index', compact('orders', 'mitras', 'kpis'));
    }
}
