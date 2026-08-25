<?php

namespace App\Http\Controllers;

use App\Models\PengajuanLibur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    public function index()
    {
        $users = User::with('pengajuanLibur')->get();
        return view('layouts.petugas.index', compact('users'));
    }

    public function create()
    {
        return view('layouts.petugas.create');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('layouts.petugas.edit', compact('user'));
    }

    public function approvalLibur()
    {
        $pengajuan = PengajuanLibur::with('user')->latest('id_libur')->first();
        $pengajuans = PengajuanLibur::with('user')->latest('id_libur')->get();
        return view('layouts.petugas.approval-libur', compact('pengajuan', 'pengajuans'));
    }

    public function ajukanLibur()
    {
        $riwayats = PengajuanLibur::latest('id_libur')->get();
        return view('layouts.petugas.ajukan-libur', compact('riwayats'));
    }
}
