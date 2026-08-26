<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobilePetugasController extends Controller
{
    // ========================================================
    // 1. PETUGAS DISTRIBUSI
    // ========================================================

    public function distribusiIndex()
    {
        return (new PetugasDistribusiController())->index();
    }

    public function distribusiRiwayat()
    {
        return (new PetugasDistribusiController())->riwayat();
    }

    public function distribusiDetail($id = null)
    {
        return (new PetugasDistribusiController())->detail($id);
    }

    public function distribusiComplete(Request $request, $id)
    {
        return (new PetugasDistribusiController())->complete($request, $id);
    }

    // ========================================================
    // 2. PETUGAS PEMBIBITAN
    // ========================================================

    public function pembibitanIndex()
    {
        return (new PetugasPembibitanController())->index();
    }

    public function pembibitanForm()
    {
        return (new PetugasPembibitanController())->form();
    }

    public function pembibitanStoreBatch(Request $request)
    {
        return (new PetugasPembibitanController())->storeBatch($request);
    }

    public function pembibitanLogPakan(Request $request)
    {
        return (new PetugasPembibitanController())->logPakan($request);
    }

    // ========================================================
    // 3. PETUGAS PEMBESARAN
    // ========================================================

    public function pembesaranIndex()
    {
        return (new PetugasPembesaranController())->index();
    }

    public function pembesaranCreateBatch()
    {
        return (new PetugasPembesaranController())->createBatch();
    }

    public function pembesaranStoreBatch(Request $request)
    {
        return (new PetugasPembesaranController())->storeBatch($request);
    }

    public function pembesaranLogPakan(Request $request)
    {
        return (new PetugasPembesaranController())->logPakan($request);
    }
}
