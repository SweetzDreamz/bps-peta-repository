@extends('layouts.app')

@section('page-title', 'History Peminjaman')

@section('content')

{{-- Alert --}}
@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="text-xl font-bold text-gray-800">History Peminjaman Peta</h3>
        <p class="text-sm text-gray-500 mt-1">
            @if(auth()->user()->isSupervisor())
                Seluruh riwayat peminjaman peta oleh semua operator
            @else
                Riwayat peminjaman peta Anda
            @endif
        </p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalSemua }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">Sedang Dipinjam</p>
            <p class="text-2xl font-bold text-orange-600">{{ $totalDipinjam }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">Dikembalikan</p>
            <p class="text-2xl font-bold text-green-600">{{ $totalDikembalikan }}</p>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('history.index') }}" class="flex flex-wrap items-end gap-3">

        {{-- Jenis Peta --}}
        <div class="min-w-32">
            <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Peta</label>
            <select name="jenis_peta"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                <option value="WA" {{ request('jenis_peta') === 'WA' ? 'selected' : '' }}>WA</option>
                <option value="WB" {{ request('jenis_peta') === 'WB' ? 'selected' : '' }}>WB</option>
                <option value="SLS" {{ request('jenis_peta') === 'SLS' ? 'selected' : '' }}>SLS</option>
            </select>
        </div>

        {{-- Kecamatan --}}
        <div class="min-w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Kecamatan</label>
            <select name="kode_kec" id="filter_kec" onchange="filterDesaChange(this.value)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                @foreach($kecamatan as $kec)
                    <option value="{{ $kec->kode_kec }}" {{ request('kode_kec') == $kec->kode_kec ? 'selected' : '' }}>
                        {{ $kec->nama_kec }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Desa --}}
        <div class="min-w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Desa/Kelurahan</label>
            <select name="kode_desa" id="filter_desa"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
            </select>
        </div>

        {{-- Kegiatan --}}
        <div class="min-w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Kegiatan</label>
            <select name="kegiatan_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                @foreach($kegiatan as $k)
                    <option value="{{ $k->id }}" {{ request('kegiatan_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kegiatan }} ({{ $k->tahun }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            </select>
        </div>

        {{-- Tanggal --}}
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('history.index') }}"
               class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-700">Daftar Riwayat Peminjaman</p>
        <p class="text-xs text-gray-400">{{ $history->total() }} transaksi ditemukan</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-12">No</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-16">Jenis</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Informasi Peta</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kegiatan</th>
                    @if(auth()->user()->isSupervisor())
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Peminjam</th>
                    @endif
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-28">Tanggal</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-28">Status</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($history as $index => $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-500 text-xs align-top">
                        {{ ($history->currentPage() - 1) * $history->perPage() + $index + 1 }}
                    </td>

                    {{-- Jenis Peta --}}
                    <td class="px-4 py-3 align-top">
                        @php $jenis = $item->peta->jenis_peta ?? '-'; @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                            {{ $jenis === 'WA' ? 'bg-blue-100 text-blue-800' : ($jenis === 'WB' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                            {{ $jenis }}
                        </span>
                    </td>

                    {{-- Informasi Peta --}}
                    <td class="px-4 py-3 align-top">
                        @if($item->peta && $item->peta->wilayah)
                        <div class="space-y-0.5 text-xs">
                            <p class="text-gray-500">KECAMATAN :
                                <span class="text-gray-800 font-medium">{{ $item->peta->wilayah->nama_kec }}</span>
                            </p>
                            <p class="text-gray-500">DESA :
                                <span class="text-gray-800 font-medium">{{ $item->peta->wilayah->nama_desa }}</span>
                            </p>
                            @if($jenis === 'WB' && $item->peta->wilayah->kode_blok)
                            <p class="text-gray-500">BLOK :
                                <span class="text-gray-800 font-mono font-medium">{{ $item->peta->wilayah->kode_blok }}</span>
                            </p>
                            @endif
                            @if($jenis === 'SLS' && $item->peta->sls)
                            <p class="text-gray-500">SLS :
                                <span class="text-gray-800 font-medium">{{ $item->peta->sls->nama_sls }}</span>
                            </p>
                            @endif
                        </div>
                        @else
                        <span class="text-gray-400 text-xs">Data tidak tersedia</span>
                        @endif
                    </td>

                    {{-- Kegiatan --}}
                    <td class="px-4 py-3 align-top">
                        <div class="text-xs">
                            <p class="text-gray-800 font-medium">{{ $item->kegiatan->nama_kegiatan ?? '-' }}</p>
                            <p class="text-gray-500 mt-0.5">
                                {{ $item->kegiatan->tanggal_mulai->format('d M Y') ?? '-' }} s/d
                                {{ $item->kegiatan->tanggal_selesai->format('d M Y') ?? '-' }}
                            </p>
                        </div>
                    </td>

                    {{-- Peminjam (supervisor only) --}}
                    @if(auth()->user()->isSupervisor())
                    <td class="px-4 py-3 align-top">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-xs">
                                <p class="text-gray-800 font-medium">{{ $item->user->name ?? '-' }}</p>
                                <p class="text-gray-500">{{ $item->user->nip ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    @endif

                    {{-- Tanggal --}}
                    <td class="px-4 py-3 align-top">
                        <div class="text-xs">
                            <p class="text-gray-800">{{ $item->tanggal->format('d M Y') }}</p>
                            @if($item->waktu_kembali)
                            <p class="text-green-600 mt-0.5">
                                Kembali: {{ $item->waktu_kembali->format('d M Y') }}
                            </p>
                            @endif
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3 align-top">
                        @if($item->status === 'dipinjam')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                Dipinjam
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Dikembalikan
                            </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3 align-top">
                        <div class="flex items-center gap-1.5">

                    {{-- Lihat file kembali — supervisor saja --}}
                    @if($item->file_kembali)
                        @if(auth()->user()->isSupervisor())
                        @php
                            $fileKembaliUrl = route('file.peta', ['path' => $item->file_kembali]);
                            $fileKembaliExt = pathinfo($item->file_kembali, PATHINFO_EXTENSION);
                        @endphp
                        <button onclick="previewFileKembali('{{ $fileKembaliUrl }}', '{{ $fileKembaliExt }}')"
                                title="Lihat File Dikembalikan"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        @endif
                    @endif

                    {{-- Pinjaman milik sendiri dan masih dipinjam --}}
                    @if($item->status === 'dipinjam' && $item->user_id === auth()->id())

                        {{-- Tombol kembalikan (semua role yang meminjam) --}}
                        <button onclick="openKembalikanModal({{ $item->id }})"
                                title="Kembalikan Peta"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </button>

                        {{-- Tombol batalkan (supervisor yang meminjam milik sendiri) --}}
                        @if(auth()->user()->isSupervisor())
                        <button onclick="openBatalkanModal({{ $item->id }})"
                                title="Batalkan Peminjaman"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        @endif

                    {{-- Pinjaman milik orang lain dan masih dipinjam --}}
                    @elseif($item->status === 'dipinjam' && $item->user_id !== auth()->id())

                        {{-- Supervisor bisa kembalikan dan batalkan pinjaman operator --}}
                        @if(auth()->user()->isSupervisor())

                        {{-- Tombol kembalikan --}}
                        <button onclick="openKembalikanModal({{ $item->id }})"
                                title="Kembalikan Peta"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                        </button>

                        {{-- Tombol batalkan --}}
                        <button onclick="openBatalkanModal({{ $item->id }})"
                                title="Batalkan Peminjaman"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        @endif

                    @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isSupervisor() ? 8 : 7 }}"
                        class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-400 text-sm">Belum ada riwayat peminjaman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($history->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $history->firstItem() }}–{{ $history->lastItem() }} dari {{ $history->total() }} transaksi
        </p>
        {{ $history->links() }}
    </div>
    @else
    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
        <p class="text-xs text-gray-500">Menampilkan {{ $history->total() }} transaksi</p>
    </div>
    @endif
</div>

{{-- ===== MODAL PREVIEW FILE KEMBALI ===== --}}
<div id="modalPreviewKembali"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-80"
     onclick="closePreviewKembali()">
    <div class="relative max-w-4xl max-h-screen p-4" onclick="event.stopPropagation()">

        {{-- Overlay transparan blokir klik kanan --}}
        <div class="absolute inset-4 z-10"
             oncontextmenu="return false;"
             ondragstart="return false;">
        </div>

        {{-- Konten gambar --}}
        <div id="previewKembaliKonten"></div>

        <button onclick="closePreviewKembali()"
                class="absolute top-2 right-2 z-20 w-8 h-8 rounded-full bg-white text-gray-800 flex items-center justify-center hover:bg-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

{{-- MODAL KEMBALIKAN --}}
<div id="modalKembalikan" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Kembalikan Peta</h3>
            <button onclick="closeKembalikanModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="formKembalikan" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-4 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-700">
                        Upload file peta yang telah diperbarui sebagai bukti pengembalian.
                        Status peminjaman akan berubah menjadi <strong>Dikembalikan</strong>.
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        File Peta Diperbarui <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="file_kembali" required accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF. Maks: 10MB</p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeKembalikanModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Kembalikan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BATALKAN --}}
<div id="modalBatalkan" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Batalkan Peminjaman</h3>
            <p class="text-sm text-gray-500 mb-1">Apakah kamu yakin ingin membatalkan peminjaman ini?</p>
            <p class="text-xs text-red-500">Data transaksi akan dihapus permanen.</p>
        </div>
        <div class="px-6 pb-5 flex gap-3">
            <button onclick="closeBatalkanModal()"
                    class="flex-1 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <form id="formBatalkan" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Ya, Batalkan
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
function openKembalikanModal(transaksiId) {
    document.getElementById('formKembalikan').action = '/history/' + transaksiId + '/kembalikan';
    document.getElementById('modalKembalikan').classList.remove('hidden');
    document.getElementById('modalKembalikan').classList.add('flex');
}
function closeKembalikanModal() {
    document.getElementById('modalKembalikan').classList.add('hidden');
    document.getElementById('modalKembalikan').classList.remove('flex');
}

// Modal Batalkan
function openBatalkanModal(transaksiId) {
    document.getElementById('formBatalkan').action = '/history/' + transaksiId + '/batalkan';
    document.getElementById('modalBatalkan').classList.remove('hidden');
    document.getElementById('modalBatalkan').classList.add('flex');
}
function closeBatalkanModal() {
    document.getElementById('modalBatalkan').classList.add('hidden');
    document.getElementById('modalBatalkan').classList.remove('flex');
}


['modalKembalikan', 'modalBatalkan', 'modalPreviewKembali'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });
});

// Filter desa dinamis
const filterKec = document.getElementById('filter_kec');
if (filterKec && filterKec.value) {
    const kodeDesaAktif = '{{ request("kode_desa") }}';
    fetch(`/api/desa-by-kec?kode_kec=${filterKec.value}`)
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('filter_desa');
            select.innerHTML = '<option value="">-- Semua --</option>';
            data.forEach(d => {
                const selected = d.kode_desa === kodeDesaAktif ? 'selected' : '';
                select.innerHTML += `<option value="${d.kode_desa}" ${selected}>${d.nama_desa}</option>`;
            });
        });
}

function filterDesaChange(kodeKec) {
    const select = document.getElementById('filter_desa');
    select.innerHTML = '<option value="">Memuat...</option>';
    if (!kodeKec) { select.innerHTML = '<option value="">-- Semua --</option>'; return; }
    fetch(`/api/desa-by-kec?kode_kec=${kodeKec}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Semua --</option>';
            data.forEach(d => {
                select.innerHTML += `<option value="${d.kode_desa}">${d.nama_desa}</option>`;
            });
        });
}

// Preview file kembali — modal dengan proteksi
function previewFileKembali(url, ext) {
    const konten = document.getElementById('previewKembaliKonten');
    const extLower = ext.toLowerCase();

    if (['jpg', 'jpeg', 'png'].includes(extLower)) {
        konten.innerHTML = `
            <img src="${url}"
                 alt="File Dikembalikan"
                 class="max-w-full max-h-screen object-contain rounded-lg select-none"
                 oncontextmenu="return false;"
                 ondragstart="return false;"/>
        `;
    } else if (extLower === 'pdf') {
        konten.innerHTML = `
            <div class="bg-white rounded-lg p-6 text-center">
                <svg class="w-16 h-16 mx-auto mb-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-700 font-medium mb-1">File PDF</p>
                <p class="text-gray-400 text-sm">File ini berformat PDF dan tidak bisa dipratinjau langsung.</p>
            </div>
        `;
    }

    document.getElementById('modalPreviewKembali').classList.remove('hidden');
    document.getElementById('modalPreviewKembali').classList.add('flex');
}

function closePreviewKembali() {
    document.getElementById('modalPreviewKembali').classList.add('hidden');
    document.getElementById('modalPreviewKembali').classList.remove('flex');
    document.getElementById('previewKembaliKonten').innerHTML = '';
}


</script>

@endsection