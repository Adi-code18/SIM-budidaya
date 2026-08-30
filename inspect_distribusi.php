<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TransaksiDistribusi;
use App\Models\MitraDistributor;

echo "--- MITRA DISTRIBUTOR ---\n";
foreach (MitraDistributor::all() as $m) {
    echo "ID: {$m->id_mitra} | Nama: {$m->nama_mitra} | Tipe: {$m->tipe_mitra}\n";
}

echo "\n--- TRANSAKSI DISTRIBUSI ---\n";
echo "Total Rows: " . TransaksiDistribusi::count() . "\n";
foreach (TransaksiDistribusi::with('mitra')->get() as $t) {
    $mitraName = $t->mitra ? $t->mitra->nama_mitra : 'NULL MITRA (' . $t->id_mitra . ')';
    echo "Trx #{$t->id_transaksi} | Mitra: {$mitraName} | Kg: {$t->Total_kg} | Status: {$t->status_order} | Tanggal: {$t->tanggal_order}\n";
}
