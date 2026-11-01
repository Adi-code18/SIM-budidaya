<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ErrorHubController extends Controller
{
    /**
     * Daftar kode error yang didukung secara resmi
     */
    protected array $supportedCodes = [
        '400', '401', '402', '403', '404', '419', '429', '500', '502', '503', '504'
    ];

    /**
     * Tampilkan Halaman Landing / Katalog Seluruh Kode Error
     */
    public function index()
    {
        return view('errors.index');
    }

    /**
     * Tampilkan Preview Tampilan Error Tertentu (Direct View Render)
     */
    public function show(Request $request, string $code)
    {
        if (!in_array($code, $this->supportedCodes) && !View::exists("errors.{$code}")) {
            abort(404, "Halaman kode error HTTP {$code} tidak ditemukan dalam katalog.");
        }

        $customMessage = $request->query('message');

        // Buat dummy HTTP Exception untuk kelengkapan data telemetri jika dibutuhkan
        $exception = new \Symfony\Component\HttpKernel\Exception\HttpException((int) $code, $customMessage ?? '');

        return response()->view("errors.{$code}", [
            'code'      => $code,
            'message'   => $customMessage,
            'exception' => $exception,
        ], (int) $code);
    }

    /**
     * Lempar exception nyata abort($code) untuk menguji penanganan error bawaan Laravel
     */
    public function simulate(string $code)
    {
        if (!in_array($code, $this->supportedCodes)) {
            abort(404, "Kode error {$code} tidak valid.");
        }

        abort((int) $code, "Ini adalah simulasi pengujian nyata pengecualian HTTP {$code} di sistem AMS Budidaya Aquafarm Priangan.");
    }
}

