@extends('layouts.admin')
@section('title', 'Profil Saya - DriveSafe')

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-6 sm:py-8 space-y-6 max-w-3xl">
  <!-- Page Header -->
  <div class="pb-4 border-b border-slate-200">
    <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">
      Pengaturan Profil
    </h1>
    <p class="text-sm text-slate-500 mt-0.5">
      Kelola informasi data diri dan kata sandi akun administrator.
    </p>
  </div>

  <!-- Clean, Single-Column Settings Form -->
  <form method="POST" action="{{ route('admin.update_profile') }}" class="space-y-6">
    @csrf
    <!-- Seksi 1: Informasi Akun -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
        <div>
          <h2 class="font-bold text-slate-900 text-sm font-heading">
            Informasi Akun
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Identitas utama administrator yang digunakan pada sistem
          </p>
        </div>
        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-mono font-medium border border-slate-200">
          Role: Administrator
        </span>
      </div>

      <div class="p-5 space-y-4 text-xs font-sans">
        <!-- Nama Lengkap -->
        <div>
          <label class="block font-medium text-slate-700 mb-1">Nama Lengkap *</label>
          <input
            type="text"
            name="full_name"
            value="{{ old('full_name', $user->full_name) }}"
            required
            class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 focus:outline-none focus:border-slate-800 transition-all"
            placeholder="Nama lengkap administrator"
          />
        </div>

        <!-- Username & NIM / NIK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
          <div>
            <label class="block font-medium text-slate-700 mb-1">Username *</label>
            <div class="relative">
              <span class="absolute left-2.5 top-2 text-xs text-slate-400 font-mono">@</span>
              <input
                type="text"
                name="username"
                value="{{ old('username', $user->username) }}"
                required
                class="w-full h-8 bg-white border border-slate-300 rounded-md pl-6 pr-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-800 transition-all font-mono"
                placeholder="username"
              />
            </div>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-1">NIM / NIK / No. Induk</label>
            <input
              type="text"
              name="nim_nik"
              value="{{ old('nim_nik', $user->nim_nik) }}"
              class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 focus:outline-none focus:border-slate-800 transition-all font-mono"
              placeholder="Nomor identitas resmi"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Seksi 2: Ubah Kata Sandi -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
        <div>
          <h2 class="font-bold text-slate-900 text-sm font-heading">
            Ubah Kata Sandi
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Kosongkan bagian ini jika tidak ingin mengubah kata sandi
          </p>
        </div>
        <span class="text-xs text-slate-400 font-mono">Opsional</span>
      </div>

      <div class="p-5 space-y-3.5 text-xs font-sans">
        <!-- Kata Sandi Saat Ini -->
        <div>
          <label class="block font-medium text-slate-700 mb-1">Kata Sandi Saat Ini</label>
          <input
            type="password"
            name="current_password"
            class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
            placeholder="Ketik kata sandi saat ini untuk verifikasi perubahan"
          />
        </div>

        <!-- Kata Sandi Baru & Konfirmasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
          <div>
            <label class="block font-medium text-slate-700 mb-1">Kata Sandi Baru</label>
            <input
              type="password"
              name="new_password"
              minlength="6"
              class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
              placeholder="Minimal 6 karakter"
            />
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
            <input
              type="password"
              name="new_password_confirmation"
              minlength="6"
              class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
              placeholder="Ulangi kata sandi baru"
            />
          </div>
        </div>
      </div>

      <!-- Footer Tombol Simpan -->
      <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end gap-2.5 font-sans">
        <a
          href="{{ route('admin.dashboard') }}"
          class="h-8 px-3.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-md text-xs font-medium transition-colors inline-flex items-center"
        >
          Batal
        </a>
        <button
          type="submit"
          class="h-8 px-3.5 bg-slate-900 hover:bg-slate-800 text-white rounded-md text-xs font-medium transition-colors inline-flex items-center gap-1.5 cursor-pointer"
        >
          <svg
            class="w-3.5 h-3.5 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M5 13l4 4L19 7"
            ></path>
          </svg>
          <span>Simpan Perubahan</span>
        </button>
      </div>
    </div>
  </form>
</div>
@endsection

