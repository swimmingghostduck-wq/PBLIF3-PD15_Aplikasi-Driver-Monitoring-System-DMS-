@extends('layouts.driver')
@section('title', 'Profil - DriveSafe')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

<!-- Page Header -->
<div class="pb-4 border-b border-slate-200/80">
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-heading">
        Profil Saya
    </h1>
    <p class="text-sm text-slate-500 mt-0.5">
        Kelola informasi akun dan kendaraan Anda.
    </p>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Profile Card -->
<section class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">

    <div class="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-heading font-bold">
                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                </div>

                <div>
                    <h2 class="text-lg font-heading font-bold text-slate-900">
                        {{ $user->full_name }}
                    </h2>

                    <p class="text-sm text-slate-500">
                        Driver
                    </p>
                </div>
            </div>

            <button
                type="button"
                onclick="openEditProfile()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit Profil
            </button>

        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 text-sm">

            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Nama Lengkap
                </dt>
                <dd class="mt-1 text-slate-900">
                    {{ $user->full_name }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Username
                </dt>
                <dd class="mt-1 text-slate-900 font-mono">
                    {{ $user->username }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Email
                </dt>
                <dd class="mt-1 text-slate-900">
                    {{ $user->email ?: '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    NIM / NIK
                </dt>
                <dd class="mt-1 text-slate-900">
                    {{ $user->nim_nik ?: '-' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Bergabung Sejak
                </dt>
                <dd class="mt-1 text-slate-900">
                    {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                </dd>
            </div>

        </div>
    </div>
</section>


<!-- Vehicle Details -->
<section class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">

    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 17h8M6 17h.01M18 17h.01M5 17H3a1 1 0 01-1-1v-3a1 1 0 011-1h1l2-5a2 2 0 011.9-1.2h8.2A2 2 0 0118 7l2 5h1a1 1 0 011 1v3a1 1 0 01-1 1h-2M6 12h12"/>
                </svg>
            </div>

            <div>
                <h2 class="text-base font-heading font-bold text-slate-900">
                    Detail Kendaraan
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Informasi kendaraan yang digunakan saat berkendara
                </p>
            </div>
        </div>

        <button
            type="button"
            onclick="openEditProfile()"
            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
        >
            Edit
        </button>

    </div>

    <div class="p-6">

        @if($user->vehicle_brand || $user->vehicle_model || $user->vehicle_plate)

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Jenis Kendaraan
                    </div>
                    <div class="mt-1.5 text-sm font-semibold text-slate-900">
                        {{ $user->vehicle_type ?: '-' }}
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Merek
                    </div>
                    <div class="mt-1.5 text-sm font-semibold text-slate-900">
                        {{ $user->vehicle_brand ?: '-' }}
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Model
                    </div>
                    <div class="mt-1.5 text-sm font-semibold text-slate-900">
                        {{ $user->vehicle_model ?: '-' }}
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Nomor Kendaraan
                    </div>
                    <div class="mt-1.5 text-sm font-bold text-slate-900 font-mono">
                        {{ $user->vehicle_plate ?: '-' }}
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 sm:col-span-2">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Tahun Kendaraan
                    </div>
                    <div class="mt-1.5 text-sm font-semibold text-slate-900">
                        {{ $user->vehicle_year ?: '-' }}
                    </div>
                </div>

            </div>

        @else

            <div class="text-center py-8">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 17h8M6 17h.01M18 17h.01M5 17H3a1 1 0 01-1-1v-3a1 1 0 011-1h1l2-5a2 2 0 011.9-1.2h8.2A2 2 0 0118 7l2 5h1a1 1 0 011 1v3a1 1 0 01-1 1h-2"/>
                    </svg>
                </div>

                <p class="mt-3 text-sm font-medium text-slate-700">
                    Belum ada data kendaraan
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Tambahkan informasi kendaraan Anda melalui Edit Profil.
                </p>

                <button
                    type="button"
                    onclick="openEditProfile()"
                    class="mt-4 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition cursor-pointer"
                >
                    Tambah Kendaraan
                </button>
            </div>

        @endif

    </div>
</section>


<!-- Monitoring Summary -->
<section class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6">

    <div class="flex items-center justify-between">
        <h2 class="text-base font-heading font-bold text-slate-900">
            Ringkasan Pemantauan
        </h2>

        <a
            href="{{ route('driver.dashboard') }}"
            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
        >
            Lihat Riwayat &rarr;
        </a>
    </div>

    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">

        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <div class="text-xs text-slate-500">Total Sesi</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">
                {{ $total_sesi ?? 0 }}
            </div>
        </div>

        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <div class="text-xs text-slate-500">Mengantuk</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">
                {{ $total_kantuk ?? 0 }}
            </div>
        </div>

        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <div class="text-xs text-slate-500">Tidak Fokus</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">
                {{ $total_distraksi ?? 0 }}
            </div>
        </div>

        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <div class="text-xs text-slate-500">Menguap</div>
            <div class="text-2xl font-bold text-slate-900 mt-1">
                {{ $total_menguap ?? 0 }}
            </div>
        </div>

    </div>
</section>


<!-- Logout -->
<div class="text-center pb-4">
    <form action="{{ route('logout') }}" method="POST" class="inline">
        @csrf
        <button
            type="submit"
            class="px-6 py-2.5 rounded-xl text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition cursor-pointer"
        >
            Logout
        </button>
    </form>
</div>

</div>

<!-- Edit Profile Modal -->

<div
    id="editProfileModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4"
>
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-heading font-bold text-slate-900">
                Edit Profil
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Perbarui informasi akun dan kendaraan Anda.
            </p>
        </div>

        <button
            type="button"
            onclick="closeEditProfile()"
            class="w-9 h-9 rounded-lg hover:bg-slate-100 text-slate-500 text-xl cursor-pointer"
        >
            &times;
        </button>
    </div>


    <form
        method="POST"
        action="{{ route('driver.update_profile') }}"
        class="p-6 space-y-6"
    >
        @csrf

        <!-- Account -->
        <div>
            <h3 class="text-sm font-bold text-slate-900 mb-4">
                Informasi Akun
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        name="full_name"
                        value="{{ old('full_name', $user->full_name) }}"
                        required
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Username
                    </label>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        required
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        NIM / NIK
                    </label>
                    <input
                        type="text"
                        name="nim_nik"
                        value="{{ old('nim_nik', $user->nim_nik) }}"
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

            </div>
        </div>


        <!-- Vehicle -->
        <div class="pt-5 border-t border-slate-200">
            <h3 class="text-sm font-bold text-slate-900 mb-4">
                Detail Kendaraan
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Jenis Kendaraan
                    </label>

                    <select
                        name="vehicle_type"
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                        <option value="">Pilih jenis</option>
                        <option value="Mobil" {{ old('vehicle_type', $user->vehicle_type) == 'Mobil' ? 'selected' : '' }}>
                            Mobil
                        </option>
                        <option value="Motor" {{ old('vehicle_type', $user->vehicle_type) == 'Motor' ? 'selected' : '' }}>
                            Motor
                        </option>
                        <option value="Truk" {{ old('vehicle_type', $user->vehicle_type) == 'Truk' ? 'selected' : '' }}>
                            Truk
                        </option>
                        <option value="Bus" {{ old('vehicle_type', $user->vehicle_type) == 'Bus' ? 'selected' : '' }}>
                            Bus
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Merek
                    </label>
                    <input
                        type="text"
                        name="vehicle_brand"
                        value="{{ old('vehicle_brand', $user->vehicle_brand) }}"
                        placeholder="Contoh: Toyota"
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Model
                    </label>
                    <input
                        type="text"
                        name="vehicle_model"
                        value="{{ old('vehicle_model', $user->vehicle_model) }}"
                        placeholder="Contoh: Avanza"
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Polisi
                    </label>
                    <input
                        type="text"
                        name="vehicle_plate"
                        value="{{ old('vehicle_plate', $user->vehicle_plate) }}"
                        placeholder="Contoh: BP 1234 XX"
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm font-mono uppercase focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tahun Kendaraan
                    </label>
                    <input
                        type="number"
                        name="vehicle_year"
                        value="{{ old('vehicle_year', $user->vehicle_year) }}"
                        min="1900"
                        max="{{ date('Y') }}"
                        placeholder="{{ date('Y') }}"
                        class="w-full h-10 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/10"
                    >
                </div>

            </div>
        </div>


        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-2">
            <button
                type="button"
                onclick="closeEditProfile()"
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold text-slate-700 transition cursor-pointer"
            >
                Batal
            </button>

            <button
                type="submit"
                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition cursor-pointer"
            >
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

</div>

<script>
function openEditProfile() {
    const modal = document.getElementById('editProfileModal');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.body.classList.add('overflow-hidden');
}

function closeEditProfile() {
    const modal = document.getElementById('editProfileModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    document.body.classList.remove('overflow-hidden');
}

document.getElementById('editProfileModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeEditProfile();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEditProfile();
    }
});
</script>

@endsection