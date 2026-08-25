<?php

namespace App\Http\Controllers;

use App\Models\Kolam;
use App\Models\ManajemenPakan;
use Illuminate\Http\Request;

class PakanController extends Controller
{
    public function index()
    {
        $kolams = Kolam::all();
        $logs = ManajemenPakan::with(['kolam', 'user'])->latest('tgl_log')->get();

        return view('layouts.pakan.index', compact('kolams', 'logs'));
    }
}
