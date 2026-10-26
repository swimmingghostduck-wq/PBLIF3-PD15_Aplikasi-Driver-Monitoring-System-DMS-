@extends('layouts.driver')
@section('title', 'Profil - DriveSafe')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-200/80">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-heading">Profil Saya</h1>
        <p class="text-sm text-slate-500 mt-0.5">Informasi akun dan ringkasan pemantauan Anda.</p>
    </div>

    <!-- Informasi Pribadi -->
    <section class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-heading font-bold" aria-hidden="true">
                {{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-heading font-bold text-slate-900">{{ Auth::user()->full_name }}</h2>
                <p class="text-sm text-slate-500 capitalize">{{ Auth::user()->role }}</p>
            </div>
        </div>
        <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div><dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Lengkap</dt><dd class="mt-0.5 text-slate-900">{{ Auth::user()->full_name }}</dd></div>
            <div><dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Username</dt><dd class="mt-0.5 text-slate-900 font-mono">{{ Auth::user()->username }}</dd></div>
            <div><dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">NIM / NIK</dt><dd class="mt-0.5 text-slate-900">{{ Auth::user()->nim_nik ?: '-' }}</dd></div>
            <div><dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bergabung Sejak</dt><dd class="mt-0.5 text-slate-900">{{ Auth::user()->created_at ? Auth::user()->created_at->format('d/m/Y') : '-' }}</dd></div>
        </dl>
    </section>

    <!-- Ringkasan Riwayat -->
    <section class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-heading font-bold text-slate-900">Ringkasan Pemantauan</h2>
            <a href="{{ route('driver.dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat Riwayat &rarr;</a>
        </div>
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4"><div class="text-xs text-slate-500">Total Sesi</div><div class="text-2xl font-bold text-slate-900 mt-1">{{ $total_sesi ?? 0 }}</div></div>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4"><div class="text-xs text-slate-500">Mengantuk</div><div class="text-2xl font-bold text-slate-900 mt-1">{{ $total_kantuk ?? 0 }}</div></div>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4"><div class="text-xs text-slate-500">Tidak Fokus</div><div class="text-2xl font-bold text-slate-900 mt-1">{{ $total_distraksi ?? 0 }}</div></div>
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4"><div class="text-xs text-slate-500">Menguap</div><div class="text-2xl font-bold text-slate-900 mt-1">{{ $total_menguap ?? 0 }}</div></div>
        </div>
    </section>

    <div class="text-center">
        <form action="{{ route('logout') }}" method="POST" class="inline"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition cursor-pointer">Logout</button></form>
    </div>
</div>
@endsection