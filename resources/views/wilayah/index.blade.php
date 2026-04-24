@extends('layouts.app')

@section('page-title', 'Data Wilayah SLS')

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
<div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-xl font-bold text-gray-800">Data Wilayah SLS</h3>
        <p class="text-sm text-gray-500 mt-1">Pengelolaan Satuan Lingkungan Setempat</p>
    </div>
    <div class="flex items-center gap-2">
        {{-- Import Excel --}}
        <button onclick="openImportModal()"
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import Excel
        </button>
        {{-- Gabung --}}
        <button id="btnGabung" onclick="toggleModeGabung()"
                class="flex items-center gap-2 {{ count($pilihanIds) > 0 ? 'hidden' : '' }} bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Gabung Wilayah
        </button>
    </div>
</div>

{{-- Bar Konfirmasi Gabung (muncul saat mode gabung aktif) --}}
<div id="barGabung" class="{{ count($pilihanIds) > 0 ? 'flex' : 'hidden' }} mb-4 px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg items-center justify-between">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm text-blue-700">
            Mode Penggabungan aktif — pilih minimal 2 wilayah SLS dari tabel
        </span>
        <span id="jumlahDipilih" class="text-sm font-bold text-blue-800">{{ count($pilihanIds) }} dipilih</span>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="openGabungModal()"
                id="btnLanjutGabung"
                {{ count($pilihanIds) < 2 ? 'disabled' : '' }}
                class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            Lanjut Gabung
        </button>
        <button onclick="batalModeGabung()"
                class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
            Batal
        </button>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('wilayah.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama SLS, desa, kecamatan..."
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Kecamatan</label>
            <select name="kode_kec"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                @foreach($kecamatan as $kec)
                    <option value="{{ $kec->kode_kec }}" {{ request('kode_kec') == $kec->kode_kec ? 'selected' : '' }}>
                        {{ $kec->nama_kec }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-32">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('wilayah.index') }}"
               class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-700">Daftar Wilayah SLS</p>
        <p class="text-xs text-gray-400">{{ $wilayah->total() }} wilayah ditemukan</p>
    </div>

    {{-- Wrapper dengan sticky column --}}
    <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
        <table class="w-full text-sm border-collapse" style="min-width: 1400px;">
            <thead class="sticky top-0 z-20">
                <tr class="bg-gray-50 border-b border-gray-200">
                    {{-- Checkbox (muncul saat mode gabung) --}}
                    <th id="thCheckbox" class="hidden px-3 py-3 text-center w-10 bg-gray-50 border-r border-gray-200">
                        <input type="checkbox" id="checkAll" onchange="toggleAll(this)"
                               class="w-4 h-4 accent-blue-600"/>
                    </th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap w-10">No</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">ID SubSLS</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Nama SLS</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Nama Ketua</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Jenis</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Kd.Prop</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Kd.Kab</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Kd.Kec</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Kd.Des</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Kd.SLS</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Kd.SubSLS</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Klas</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Nama Kec</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Nama Des</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">KK</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">BTT</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">BTTK</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">BKU</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">BBtt Non</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Usaha</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Muatan</th>
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Status</th>
                    {{-- Frozen column --}}
                    <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap bg-gray-50"
                        style="position: sticky; right: 0; z-index: 30; box-shadow: -2px 0 4px rgba(0,0,0,0.08);">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($wilayah as $index => $item)
                <tr class="hover:bg-gray-50 transition-colors {{ $item->status === 'nonaktif' ? 'opacity-60' : '' }}"
                    id="row-{{ $item->id }}">

                    {{-- Checkbox --}}
                    <td id="tdCheckbox-{{ $item->id }}" class="hidden px-3 py-3 text-center border-r border-gray-100">
                        <input type="checkbox"
                            class="row-check w-4 h-4 accent-blue-600"
                            value="{{ $item->id }}"
                            data-nama="{{ $item->nama_sls }}"
                            {{ in_array($item->id, $pilihanIds) ? 'checked' : '' }}
                            onchange="togglePilihan(this)"/>
                    </td>

                    <td class="px-3 py-3 text-gray-500 text-xs">
                        {{ ($wilayah->currentPage() - 1) * $wilayah->perPage() + $index + 1 }}
                    </td>
                    <td class="px-3 py-3 text-xs font-mono text-gray-600">{{ $item->id_subsls ?? '-' }}</td>
                    <td class="px-3 py-3 font-medium text-gray-800">{{ $item->nama_sls }}</td>
                    <td class="px-3 py-3 text-gray-600 text-xs">{{ $item->nama_ketua ?? '-' }}</td>
                    <td class="px-3 py-3 text-gray-600 text-xs">{{ $item->jenis ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-mono">{{ $item->kode_prop ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-mono">{{ $item->kode_kab ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-mono">{{ $item->kode_kec ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-mono">{{ $item->kode_des ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-mono">{{ $item->kode_sls ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-mono">{{ $item->kode_subsls ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ $item->klas ?? '-' }}</td>
                    <td class="px-3 py-3 text-gray-600 text-xs">{{ $item->nama_kec ?? '-' }}</td>
                    <td class="px-3 py-3 text-gray-600 text-xs">{{ $item->nama_des ?? '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ number_format($item->kk) }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ number_format($item->btt) }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ number_format($item->bttk) }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ number_format($item->bku) }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ number_format($item->bbtt_nonusaha) }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ number_format($item->usaha) }}</td>
                    <td class="px-3 py-3 text-center text-xs">{{ number_format($item->muatan) }}</td>
                    <td class="px-3 py-3 text-center">
                        @if($item->status === 'aktif')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                        @endif
                    </td>

                    {{-- Frozen Aksi Column --}}
                    <td class="px-3 py-3 bg-white"
                        style="position: sticky; right: 0; z-index: 10; box-shadow: -2px 0 4px rgba(0,0,0,0.08);">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Tombol Detail/Mata --}}
                            <a href="{{ route('wilayah.show', $item) }}"
                               title="Detail & Edit"
                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            {{-- Tombol Pecah --}}
                            @if($item->status === 'aktif')
                            <button onclick="openPecahModal({{ $item->id }}, '{{ $item->nama_sls }}')"
                                    title="Pecah Wilayah"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </button>
                            @endif
                            {{-- Tombol Hapus --}}
                            <button onclick="openHapusModal({{ $item->id }}, '{{ $item->nama_sls }}')"
                                    title="Hapus"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="25" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <p class="text-gray-400 text-sm">Belum ada data wilayah</p>
                        <button onclick="openImportModal()"
                                class="mt-3 px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Import Data Excel
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($wilayah->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $wilayah->firstItem() }}–{{ $wilayah->lastItem() }} dari {{ $wilayah->total() }} wilayah
        </p>
        {{ $wilayah->links() }}
    </div>
    @else
    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
        <p class="text-xs text-gray-500">Menampilkan {{ $wilayah->total() }} wilayah</p>
    </div>
    @endif
</div>

{{-- MODAL IMPORT --}}
<div id="modalImport" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Import Data Wilayah</h3>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('wilayah.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-4 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-700 font-medium mb-1">Format kolom Excel yang diperlukan:</p>
                    <p class="text-xs text-blue-600 font-mono">
                        id_subsls | nama_sls | nama_ketua | jenis | kode_prop | kode_kab | kode_kec | kode_des | kode_sls | kode_subsls | klas | nama_prop | nama_kab | nama_kec | nama_des | kk | btt | bttk | bku | bbtt_nonusaha | usaha | muatan
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        File Excel <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="file_excel" required accept=".xlsx,.xls,.csv"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    <p class="text-xs text-gray-400 mt-1">Format: XLSX, XLS, CSV. Maks: 5MB</p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeImportModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL GABUNG --}}
<div id="modalGabung" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Gabung Wilayah SLS</h3>
            <button onclick="closeGabungModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('wilayah.gabung') }}" id="formGabung">
            @csrf
            <div id="inputWilayahIds"></div>
            <div class="px-6 py-4 space-y-4">

                {{-- Info wilayah dipilih --}}
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-medium text-gray-600 mb-2">Wilayah yang akan digabung:</p>
                    <ul id="listWilayahDipilih" class="space-y-1"></ul>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Tipe Penggabungan <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_gabung" id="tipe_gabung" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="baru">Buat wilayah baru</option>
                        <option value="ikut_pertama">Ikut wilayah pertama dipilih</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Wilayah Hasil Gabungan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_baru" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: RT 01 RW 05"/>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Ketua</label>
                        <input type="text" name="nama_ketua"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Opsional"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis</label>
                        <input type="text" name="jenis"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: RT"/>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <p class="text-xs text-orange-700">
                        Wilayah yang digabung akan berubah status menjadi <strong>Nonaktif</strong>.
                        Wilayah hasil gabungan akan berstatus <strong>Aktif</strong>.
                    </p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="closeGabungModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Gabung Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PECAH --}}
<div id="modalPecah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Pecah Wilayah SLS</h3>
            <button onclick="closePecahModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="formPecah">
            @csrf
            <div class="px-6 py-4 space-y-4">

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Memecah wilayah:</p>
                    <p class="text-sm font-semibold text-gray-800" id="namaPecah"></p>
                </div>

                <div id="containerPecahan" class="space-y-3">
                    {{-- Pecahan dinamis --}}
                </div>

                <button type="button" onclick="tambahPecahan()"
                        class="w-full py-2 text-sm text-blue-600 border border-blue-200 border-dashed rounded-lg hover:bg-blue-50 transition-colors">
                    + Tambah Pecahan
                </button>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <p class="text-xs text-orange-700">
                        Wilayah asal akan berubah status menjadi <strong>Nonaktif</strong>.
                        Semua wilayah pecahan akan berstatus <strong>Aktif</strong>.
                    </p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="closePecahModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">
                    Pecah Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="modalHapus" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Hapus Wilayah</h3>
            <p class="text-sm text-gray-500">Apakah kamu yakin ingin menghapus</p>
            <p class="text-sm font-semibold text-gray-800 my-2" id="hapus_nama"></p>
            <p class="text-xs text-red-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-6 pb-5 flex gap-3">
            <button onclick="closeHapusModal()"
                    class="flex-1 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                Batal
            </button>
            <form id="formHapus" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let modeGabung = {{ count($pilihanIds) > 0 ? 'true' : 'false' }};

// ===== INIT — tampilkan checkbox jika mode gabung aktif =====
document.addEventListener('DOMContentLoaded', function() {
    if (modeGabung) {
        document.getElementById('thCheckbox').classList.remove('hidden');
        document.querySelectorAll('[id^="tdCheckbox-"]').forEach(td => td.classList.remove('hidden'));
        updateJumlahUI({{ count($pilihanIds) }});
    }
});

// ===== TOGGLE PILIHAN via AJAX =====
function togglePilihan(cb) {
    fetch('/wilayah/pilihan/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ id: cb.value })
    })
    .then(r => r.json())
    .then(data => {
        updateJumlahUI(data.total);
    });
}

function updateJumlahUI(total) {
    document.getElementById('jumlahDipilih').textContent = total + ' dipilih';
    const btn = document.getElementById('btnLanjutGabung');
    if (btn) btn.disabled = total < 2;
}

// ===== MODE GABUNG =====
function toggleModeGabung() {
    modeGabung = true;
    document.getElementById('barGabung').classList.remove('hidden');
    document.getElementById('barGabung').classList.add('flex');
    document.getElementById('btnGabung').classList.add('hidden');
    document.getElementById('thCheckbox').classList.remove('hidden');
    document.querySelectorAll('[id^="tdCheckbox-"]').forEach(td => td.classList.remove('hidden'));
}

function batalModeGabung() {
    // Hapus semua pilihan di session
    fetch('/wilayah/pilihan/clear', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => {
        modeGabung = false;
        document.getElementById('barGabung').classList.add('hidden');
        document.getElementById('barGabung').classList.remove('flex');
        document.getElementById('btnGabung').classList.remove('hidden');
        document.getElementById('thCheckbox').classList.add('hidden');
        document.querySelectorAll('[id^="tdCheckbox-"]').forEach(td => td.classList.add('hidden'));
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
        document.getElementById('checkAll').checked = false;
        updateJumlahUI(0);
    });
}

function toggleAll(master) {
    const checks = document.querySelectorAll('.row-check');
    const promises = [];
    checks.forEach(cb => {
        if (cb.checked !== master.checked) {
            cb.checked = master.checked;
            promises.push(
                fetch('/wilayah/pilihan/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ id: cb.value })
                })
            );
        }
    });
    Promise.all(promises).then(() => {
        fetch('/wilayah/pilihan/list')
            .then(r => r.json())
            .then(data => updateJumlahUI(data.ids.length));
    });
}

// ===== MODAL GABUNG =====
function openGabungModal() {
    fetch('/wilayah/pilihan/list')
        .then(r => r.json())
        .then(data => {
            if (data.ids.length < 2) {
                alert('Pilih minimal 2 wilayah untuk digabung.');
                return;
            }

            // Isi hidden inputs
            let inputHtml = '';
            let listHtml  = '';
            data.data.forEach((w, i) => {
                inputHtml += `<input type="hidden" name="wilayah_ids[]" value="${w.id}"/>`;
                listHtml  += `<li class="text-xs text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0">${i+1}</span>
                    ${w.nama_sls} — ${w.nama_des ?? ''}
                </li>`;
            });

            document.getElementById('inputWilayahIds').innerHTML = inputHtml;
            document.getElementById('listWilayahDipilih').innerHTML = listHtml;
            document.getElementById('modalGabung').classList.remove('hidden');
            document.getElementById('modalGabung').classList.add('flex');
        });
}

function closeGabungModal() {
    document.getElementById('modalGabung').classList.add('hidden');
    document.getElementById('modalGabung').classList.remove('flex');
}

// Setelah form gabung submit, clear session
document.getElementById('formGabung').addEventListener('submit', function() {
    fetch('/wilayah/pilihan/clear', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    });
});

// ===== PECAH =====
let pecahanCount = 0;

function openPecahModal(id, nama) {
    document.getElementById('namaPecah').textContent = nama;
    document.getElementById('formPecah').action = '/wilayah/' + id + '/pecah';
    document.getElementById('containerPecahan').innerHTML = '';
    pecahanCount = 0;
    tambahPecahan();
    tambahPecahan();
    document.getElementById('modalPecah').classList.remove('hidden');
    document.getElementById('modalPecah').classList.add('flex');
}

function closePecahModal() {
    document.getElementById('modalPecah').classList.add('hidden');
    document.getElementById('modalPecah').classList.remove('flex');
}

function tambahPecahan() {
    const i = pecahanCount++;
    const html = `
        <div class="border border-gray-200 rounded-lg p-3 space-y-2" id="pecahan-${i}">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-600">Pecahan ${i + 1}</p>
                ${i >= 2 ? `<button type="button" onclick="hapusPecahan(${i})"
                    class="text-red-400 hover:text-red-600 text-xs">Hapus</button>` : ''}
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="col-span-2">
                    <input type="text" name="pecahan[${i}][nama]" required
                           placeholder="Nama SLS *"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <input type="text" name="pecahan[${i}][jenis]"
                       placeholder="Jenis (opsional)"
                       class="border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                <input type="text" name="pecahan[${i}][ketua]"
                       placeholder="Ketua (opsional)"
                       class="border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                <input type="number" name="pecahan[${i}][kk]" min="0"
                       placeholder="Jumlah KK"
                       class="border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
        </div>`;
    document.getElementById('containerPecahan').insertAdjacentHTML('beforeend', html);
}

function hapusPecahan(i) {
    document.getElementById('pecahan-' + i)?.remove();
}

// ===== HAPUS =====
function openHapusModal(id, nama) {
    document.getElementById('hapus_nama').textContent = nama + '?';
    document.getElementById('formHapus').action = '/wilayah/' + id;
    document.getElementById('modalHapus').classList.remove('hidden');
    document.getElementById('modalHapus').classList.add('flex');
}
function closeHapusModal() {
    document.getElementById('modalHapus').classList.add('hidden');
    document.getElementById('modalHapus').classList.remove('flex');
}

// ===== IMPORT =====
function openImportModal() {
    document.getElementById('modalImport').classList.remove('hidden');
    document.getElementById('modalImport').classList.add('flex');
}
function closeImportModal() {
    document.getElementById('modalImport').classList.add('hidden');
    document.getElementById('modalImport').classList.remove('flex');
}

// Tutup modal klik luar
['modalImport', 'modalGabung', 'modalPecah', 'modalHapus'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });
});
</script>

@endsection