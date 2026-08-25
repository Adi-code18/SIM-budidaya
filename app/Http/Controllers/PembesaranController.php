<?php

namespace App\Http\Controllers;

use App\Models\BatchPembesaran;
use App\Models\Kolam;
use Illuminate\Http\Request;

class PembesaranController extends Controller
{
    public function index()
    {
        $batchRecords = BatchPembesaran::with(['kolam', 'user'])->latest('id_pembesaran')->get();
        $kolams = Kolam::all();

        $totalBiomassa = BatchPembesaran::sum('biomassa_est') / 1000; // in Ton
        $avgFcr = BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr') ?? 1.12;

        return view('layouts.pembesaran.index', compact('batchRecords', 'kolams', 'totalBiomassa', 'avgFcr'));
    }
}
