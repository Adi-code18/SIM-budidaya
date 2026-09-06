<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM-BUDIDAYA Mobile Splash</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            700: '#1E3E62',
                            800: '#0F2C59',
                            900: '#0B192C',
                            950: '#051120'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100 flex items-center justify-center p-0 sm:p-4 antialiased">
    
    @php
        $currentRole = $role ?? 'distribusi';
        if ($currentRole === 'pembibitan') {
            $roleTitle = 'Mobile Petugas Pembibitan';
            $targetLogin = route('petugas.pembibitan.login');
        } elseif ($currentRole === 'pembesaran') {
            $roleTitle = 'Mobile Petugas Pembesaran';
            $targetLogin = route('petugas.pembesaran.login');
        } else {
            $roleTitle = 'Mobile Petugas Distribusi';
            $targetLogin = route('mobile.petugas.login');
        }
    @endphp

    <div class="w-full sm:max-w-md min-h-screen sm:min-h-[560px] bg-gradient-to-b from-navy-800 via-navy-900 to-navy-950 text-white flex flex-col items-center justify-between p-8 relative overflow-hidden sm:rounded-3xl sm:shadow-2xl sm:my-8"
         x-data="{ 
             init() {
                 setTimeout(() => {
                     window.location.href = '{{ $targetLogin }}';
                 }, 2200);
             }
         }">

        <!-- Background glowing graphics -->
        <div class="absolute -top-16 -left-16 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-16 -right-16 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl"></div>

        <div class="my-auto flex flex-col items-center text-center space-y-6 relative z-10">
            
            <!-- White Logo Box Container -->
            <div class="w-36 h-36 rounded-3xl bg-white p-4 shadow-2xl flex flex-col items-center justify-center space-y-2 border border-white/20 transform transition-transform hover:scale-105">
                <img src="{{ asset('build/images/Logo aquafarm.png') }}" 
                     alt="Logo Aquafarm" 
                     class="w-16 h-16 object-contain drop-shadow-sm">
                <span class="text-xs font-black tracking-tighter text-navy-900 uppercase">SIM-BUDIDAYA</span>
            </div>

            <div class="space-y-1">
                <h1 class="text-2xl font-extrabold text-white tracking-wide">SIM-BUDIDAYA</h1>
                <p class="text-[10px] tracking-[0.2em] font-extrabold text-sky-400 uppercase">{{ $roleTitle }}</p>
            </div>

            <!-- Spinner -->
            <div class="pt-4">
                <div class="w-6 h-6 border-2 border-white/20 border-t-sky-400 rounded-full animate-spin mx-auto"></div>
            </div>

        </div>

        <!-- Bottom Click Alternative -->
        <div class="relative z-10 text-center pb-4">
            <a href="{{ $targetLogin }}" class="text-xs text-sky-300 font-bold hover:underline">
                Ketuk untuk melanjutkan &rarr;
            </a>
        </div>

    </div>

</body>
</html>
