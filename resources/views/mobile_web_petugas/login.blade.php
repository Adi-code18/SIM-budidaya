@extends('mobile_web_petugas.petugas_distribusi.layout')

@section('title', 'Login Mobile - SIM-BUDIDAYA')
@section('hide_header', true)
@section('hide_nav', true)

@section('content')
<div class="min-h-full px-6 py-8 flex flex-col justify-between bg-white" x-data="{
    email: '',
    password: '',
    showPass: false,
    handleLogin() {
        if (!this.email || !this.password) {
            triggerToast('Mohon isi Email/No. HP dan Kata Kunci', 'error');
            return;
        }
        triggerToast('Login berhasil! Selamat datang.', 'success');
        setTimeout(() => {
            window.location.href = '{{ route('mobile.petugas.pengiriman') }}';
        }, 1000);
    }
}">

    <!-- Top Logo Section -->
    <div class="pt-8 flex flex-col items-center text-center space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-navy-800 flex flex-col items-center justify-center text-white shadow-lg space-y-1">
            <i class="fa-solid fa-fish-fins text-2xl text-sky-300"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-navy-900 tracking-tight">Selamat Datang Kembali</h1>
            <p class="text-xs text-slate-500 font-medium mt-1 max-w-xs leading-relaxed">
                Masuk untuk mengakses akun SIM-BUDIDAYA Anda untuk manajemen operasional.
            </p>
        </div>
    </div>

    <!-- Login Form Section -->
    <form @submit.prevent="handleLogin()" class="space-y-4 my-8">
        
        <!-- Input 1: Email / No. HP -->
        <div class="space-y-1">
            <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">EMAIL / NO. HP</label>
            <div class="relative">
                <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" 
                       x-model="email"
                       placeholder="Masukkan email atau no. hp"
                       class="w-full pl-9 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800 transition-all">
            </div>
        </div>

        <!-- Input 2: Password -->
        <div class="space-y-1">
            <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">KATA KUNCI</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                <input :type="showPass ? 'text' : 'password'" 
                       x-model="password"
                       placeholder="Masukkan kata kunci"
                       class="w-full pl-9 pr-10 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800 transition-all">
                <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-3.5 text-slate-400 hover:text-slate-600">
                    <i class="fa-solid" :class="showPass ? 'fa-eye-slash text-xs' : 'fa-eye text-xs'"></i>
                </button>
            </div>
            <div class="flex justify-end pt-1">
                <a href="#" @click.prevent="triggerToast('Silakan hubungi administrator SIM-BUDIDAYA untuk reset password.', 'info')" 
                   class="text-[11px] font-bold text-sky-700 hover:underline">
                    Lupa Kata Sandi?
                </a>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full py-3.5 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
            <span>Masuk</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>

    </form>

    <!-- Footer Support Section -->
    <div class="text-center pt-4 pb-2">
        <p class="text-[11px] text-slate-400 font-medium">
            Membutuhkan bantuan? 
            <a href="https://wa.me/6281234567890" target="_blank" class="text-sky-700 font-bold hover:underline">Hubungi Support</a>
        </p>
    </div>

</div>
@endsection
