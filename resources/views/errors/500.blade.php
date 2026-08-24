<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kesalahan Server - SIM-BUDIDAYA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-xl border border-slate-200 text-center space-y-4">
        <div class="w-16 h-16 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mx-auto text-2xl font-bold">
            500
        </div>
        <h1 class="text-xl font-extrabold text-slate-900">Terjadi Gangguan Server</h1>
        <p class="text-xs text-slate-600 leading-relaxed">
            {{ $message ?? 'Sistem sedang memproses permintaan atau mengalami gangguan sementara. Detail teknis telah dicatat secara aman.' }}
        </p>
        <div class="pt-4">
            <a href="{{ route('dashboard') }}" class="py-2.5 px-5 bg-[#051B44] hover:bg-[#09265c] text-white font-bold text-xs rounded-xl transition-all inline-block">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
