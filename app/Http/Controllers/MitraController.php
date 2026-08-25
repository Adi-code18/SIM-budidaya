<?php

namespace App\Http\Controllers;

use App\Models\MitraDistributor;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index()
    {
        $mitraRecords = MitraDistributor::with(['user', 'transaksiDistribusi'])->get();

        $mitras = [];
        $images = [
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=120',
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=120',
            'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&q=80&w=120'
        ];

        foreach ($mitraRecords as $idx => $m) {
            $tipeKey = strtolower(explode(' ', $m->tipe_mitra)[0] ?? 'distributor');

            $mitras[] = [
                'id'        => 'MTR-2023-' . str_pad($m->id_mitra, 3, '0', STR_PAD_LEFT),
                'nama'      => $m->nama_mitra,
                'tipe'      => $m->tipe_mitra,
                'tipeKey'   => $tipeKey,
                'alamat'    => $m->alamat,
                'wilayah'   => 'jakarta',
                'lat'       => (string) ($m->latitude ?? -6.208800),
                'lng'       => (string) ($m->longitude ?? 106.845600),
                'kontak'    => '+62 812-3456-7890',
                'email'     => 'contact@' . strtolower(str_replace(' ', '', $m->nama_mitra)) . '.id',
                'image'     => $images[$idx % count($images)]
            ];
        }

        return view('layouts.mitra.index', compact('mitras'));
    }
}
