@extends('layouts.admin')
@section('title', 'Kelola Pengemudi - DriveSafe')

@section('header_actions')
<!-- Action di Topbar: Tambah Pengemudi -->
<button type="button" onclick="openCreateModal()" 
        class="h-8 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-md transition-colors inline-flex items-center gap-1.5 cursor-pointer">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
    </svg>
    <span>Tambah Pengemudi</span>
</button>
@endsection

@section('content')
<div class="px-6 sm:px-8 lg:px-10 py-6 sm:py-8 space-y-5">
    
    <!-- Page Header & Metrics Summary -->
    <div class="pb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 tracking-tight">Direktori Pengemudi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manajemen akun pengemudi, riwayat pemantauan sesi, dan pemantauan skor keselamatan armada.</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
            <span>Total: <strong class="text-slate-900 tabular-nums">{{ $drivers->count() }}</strong> pengemudi terdaftar</span>
        </div>
    </div>

    <!-- Scannable Directory Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-mono font-semibold text-slate-500 border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Pengemudi</th>
                        <th class="px-4 py-3 whitespace-nowrap">Nomor Identitas</th>
                        <th class="px-4 py-3 whitespace-nowrap">Status Akun</th>
                        <th class="px-4 py-3 whitespace-nowrap">Skor Keselamatan</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Riwayat Insiden</th>
                        <th class="px-4 py-3 whitespace-nowrap">Terdaftar Sejak</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans">
                    @forelse($drivers as $d)
                    @php
                        $alertCount = $d->alerts_count ?? $d->alerts->count();
                        $driverScore = $alertCount === 0 ? 100.0 : max(50.0, 100.0 - ($alertCount * 5.0));
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- Info Pengemudi -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded bg-slate-800 text-slate-200 flex items-center justify-center font-bold text-[11px] font-mono shrink-0">
                                    {{ strtoupper(substr($d->full_name ?? 'DR', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-slate-900 text-xs">{{ $d->full_name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ '@' . $d->username }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Identitas (NIM / NIK) -->
                        <td class="px-4 py-3 font-mono whitespace-nowrap text-xs text-slate-600 tabular-nums">
                            {{ $d->nim_nik ?? '-' }}
                        </td>

                        <!-- Status Akun -->
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($d->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <!-- Skor Keselamatan Individu -->
                        <td class="px-4 py-3 whitespace-nowrap tabular-nums font-mono">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold {{ $driverScore >= 85 ? 'text-slate-900' : ($driverScore >= 70 ? 'text-amber-700' : 'text-rose-600') }}">
                                    {{ number_format($driverScore, 1) }}%
                                </span>
                                @if($driverScore >= 85)
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Optimal</span>
                                @elseif($driverScore >= 70)
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Waspada</span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">Perhatian</span>
                                @endif
                            </div>
                        </td>

                        <!-- Total Insiden -->
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.alerts', ['driver_id' => $d->id]) }}" 
                               class="text-xs font-mono font-medium text-slate-700 hover:text-slate-900 hover:underline inline-flex items-center gap-1"
                               title="Filter riwayat insiden pengemudi ini">
                                <span class="tabular-nums font-bold">{{ $alertCount }}</span>
                                <span class="text-slate-400">insiden &rarr;</span>
                            </a>
                        </td>

                        <!-- Tanggal Terdaftar -->
                        <td class="px-4 py-3 text-slate-500 font-mono whitespace-nowrap tabular-nums text-xs">
                            {{ $d->created_at ? $d->created_at->format('d/m/Y') : '-' }}
                        </td>

                        <!-- Tombol Aksi -->
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" 
                                        class="btn-edit-driver h-7 px-2 bg-white hover:bg-slate-50 text-slate-700 rounded-md border border-slate-200 transition-colors inline-flex items-center gap-1 cursor-pointer text-xs font-medium"
                                        data-id="{{ $d->id }}"
                                        data-fullname="{{ $d->full_name }}"
                                        data-username="{{ $d->username }}"
                                        data-nim="{{ $d->nim_nik ?? '' }}"
                                        title="Ubah data pengemudi">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('admin.delete_driver', $d->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengemudi {{ $d->full_name }}?');">
                                    @csrf
                                    <button type="submit" 
                                            class="text-slate-400 hover:text-rose-600 p-1 rounded hover:bg-rose-50 transition-colors cursor-pointer" 
                                            title="Hapus akun pengemudi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center font-sans">
                            <div class="max-w-sm mx-auto space-y-1.5">
                                <h3 class="font-semibold text-slate-900 text-xs">Belum ada akun pengemudi terdaftar</h3>
                                <p class="text-[11px] text-slate-400">Gunakan tombol "+ Tambah Pengemudi" di atas untuk menambahkan pengemudi baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PENGEMUDI BARU -->
<div id="createDriverModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-lg max-w-md w-full overflow-hidden shadow-sm animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <h3 class="font-bold text-slate-900 text-sm font-heading">Tambah Pengemudi Baru</h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-md hover:bg-slate-200 transition-colors cursor-pointer" title="Tutup Modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.create_driver') }}" class="p-5 space-y-3.5 text-xs font-sans">
            @csrf
            <div>
                <label class="block font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="full_name" required
                       class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all"
                       placeholder="Nama lengkap pengemudi">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">NIM / NIK</label>
                    <input type="text" name="nim_nik"
                           class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                           placeholder="Nomor identitas">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Username *</label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-2 text-xs text-slate-400 font-mono">@</span>
                        <input type="text" name="username" required
                               class="w-full h-8 bg-white border border-slate-300 rounded-md pl-6 pr-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                               placeholder="username">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-medium text-slate-700 mb-1">Kata Sandi Akun *</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                       placeholder="Minimal 6 karakter">
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 font-sans">
                <button type="button" onclick="closeCreateModal()" class="h-8 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-xs font-medium transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="h-8 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-md text-xs font-medium transition-all cursor-pointer">
                    Simpan Pengemudi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PENGEMUDI -->
<div id="editDriverModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-lg max-w-md w-full overflow-hidden shadow-sm animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <h3 class="font-bold text-slate-900 text-sm font-heading">Ubah Data Pengemudi</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-md hover:bg-slate-200 transition-colors cursor-pointer" title="Tutup Modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="editDriverForm" method="POST" action="#" class="p-5 space-y-3.5 text-xs font-sans">
            @csrf
            <div>
                <label class="block font-medium text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="edit_full_name" name="full_name" required
                       class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 focus:outline-none focus:border-slate-800 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Username *</label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-2 text-xs text-slate-400 font-mono">@</span>
                        <input type="text" id="edit_username" name="username" required
                               class="w-full h-8 bg-white border border-slate-300 rounded-md pl-6 pr-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-800 transition-all font-mono">
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">NIM / NIK</label>
                    <input type="text" id="edit_nim_nik" name="nim_nik"
                           class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 focus:outline-none focus:border-slate-800 transition-all font-mono">
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <label class="block font-medium text-slate-700 mb-1 flex items-center justify-between">
                    <span>Ganti Kata Sandi</span>
                    <span class="text-slate-400 font-normal text-[11px] font-mono">(Kosongkan bila tidak diubah)</span>
                </label>
                <input type="password" id="edit_new_password" name="new_password" minlength="6"
                       class="w-full h-8 bg-white border border-slate-300 rounded-md px-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-800 transition-all font-mono"
                       placeholder="Kata sandi baru">
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 font-sans">
                <button type="button" onclick="closeEditModal()" class="h-8 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-xs font-medium transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="h-8 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-md text-xs font-medium transition-all cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createDriverModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createDriverModal').classList.add('hidden');
    }

    function openEditModal(driverId, fullName, username, nimNik) {
        document.getElementById('editDriverForm').action = '/admin/drivers/edit/' + driverId;
        document.getElementById('edit_full_name').value = fullName || '';
        document.getElementById('edit_username').value = username || '';
        document.getElementById('edit_nim_nik').value = nimNik || '';
        document.getElementById('edit_new_password').value = '';
        document.getElementById('editDriverModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editDriverModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-edit-driver').forEach(function(button) {
            button.addEventListener('click', function() {
                openEditModal(
                    this.getAttribute('data-id'),
                    this.getAttribute('data-fullname'),
                    this.getAttribute('data-username'),
                    this.getAttribute('data-nim')
                );
            });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endsection

