@extends('layouts.app')

@section('title', 'Peta')

@section('page-title', $jenis === 'WA' ? 'Peta WA' : 'Peta WS')

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
        <div class="flex items-center gap-3 mb-1">
            {{-- Tab WA / WS --}}
            <a href="{{ route('peta.wa') }}"
            class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                    {{ $jenis === 'WA' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                WA
            </a>
            <a href="{{ route('peta.ws') }}"
            class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                    {{ $jenis === 'WS' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                WS
            </a>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Repositori Peta — {{ $jenis }}</h3>
        <p class="text-sm text-gray-500 mt-1">
            @if($jenis === 'WA') Peta Wilayah Administrasi
            @else Peta Wilayah Statistik
            @endif
        </p>
    </div>
    @if(auth()->user()->isSupervisor())
    <button onclick="openTambahModal()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Peta
    </button>
    @endif
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-end gap-3">

        {{-- Filter Kecamatan --}}
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Kecamatan</label>
            <select name="kode_kec" id="filter_kec" onchange="filterDesaChange(this.value)" 
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                @foreach($kecamatan as $kec)
                    <option value="{{ $kec->kode_kec }}" {{ request('kode_kec') == $kec->kode_kec ? 'selected' : '' }}>
                        [{{ str_pad($kec->kode_kec, 3, '0', STR_PAD_LEFT) }}] {{ $kec->nama_kec }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Desa --}}
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Desa/Kelurahan</label>
            <select name="kode_des" id="filter_desa"
                    @if($jenis === 'SLS') onchange="filterSlsChange(this.value)" @endif
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
            </select>
        </div>

        {{-- Filter SLS — hanya untuk jenis SLS --}}
        @if($jenis === 'SLS')
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">SLS</label>
            <select name="wilayah_id" id="filter_sls"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
            </select>
        </div>
        @endif

        {{-- Filter Kegiatan --}}
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Kegiatan</label>
            <select name="kegiatan_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                @foreach($kegiatan as $k)
                    <option value="{{ $k->id }}" {{ request('kegiatan_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kegiatan }} ({{ $k->tanggal_mulai->format('Y') }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Status Wilayah --}}
        <div class="min-w-32">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status Wilayah</label>
            <select name="status_wilayah"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Aktif saja</option>
                <option value="nonaktif" {{ request('status_wilayah') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                <option value="semua" {{ request('status_wilayah') === 'semua' ? 'selected' : '' }}>Semua</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ request()->url() }}"
               class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-700">Daftar Peta {{ $jenis }}</p>
        <p class="text-xs text-gray-400">{{ $peta->total() }} Peta ditemukan</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-12">No</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-32">Peta</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Informasi Wilayah</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Informasi Kegiatan</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($peta as $index => $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-500 text-xs align-top">
                        {{ ($peta->currentPage() - 1) * $peta->perPage() + $index + 1 }}
                    </td>

                    {{-- Thumbnail Peta --}}
                    <td class="px-4 py-3 align-top">
                        @php $ext = pathinfo($item->path_file, PATHINFO_EXTENSION); @endphp
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                            <img src="{{ Storage::url($item->path_file) }}"
                                 alt="Peta {{ $jenis }}"
                                 class="w-28 h-20 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-80 transition-opacity"
                                 onclick="previewGambar('{{ Storage::url($item->path_file) }}')"/>
                        @else
                            <div class="w-28 h-20 rounded-lg border border-gray-200 bg-red-50 flex flex-col items-center justify-center gap-1 cursor-pointer hover:bg-red-100 transition-colors"
                                 onclick="previewGambar('{{ Storage::url($item->path_file) }}')">
                                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs text-red-500 font-medium">PDF</span>
                            </div>
                        @endif
                    </td>

                    {{-- Informasi Wilayah --}}
                    <td class="px-4 py-3 align-top">
                        @if($item->wilayah)
                        <div class="space-y-0.5 text-xs">
                            <p class="text-gray-500">PROVINSI :
                                <span class="text-gray-800 font-medium">{{ $item->wilayah->nama_prop ?? '-' }}</span>
                            </p>
                            <p class="text-gray-500">KAB/KOTA :
                                <span class="text-gray-800 font-medium">{{ $item->wilayah->nama_kab ?? '-' }}</span>
                            </p>
                            <p class="text-gray-500">KECAMATAN :
                                <span class="text-gray-800 font-medium">{{ $item->wilayah->nama_kec ?? '-' }}</span>
                            </p>
                            <p class="text-gray-500">DESA :
                                <span class="text-gray-800 font-medium">{{ $item->wilayah->nama_des ?? '-' }}</span>
                            </p>
                            {{-- SLS hanya untuk jenis SLS --}}
                            @if($jenis === 'SLS')
                            <p class="text-gray-500">SLS :
                                <span class="text-gray-800 font-medium">{{ $item->wilayah->nama_sls ?? '-' }}</span>
                            </p>
                            @endif
                            {{-- Badge status wilayah --}}
                            <div class="pt-1">
                                @if(($item->wilayah->status ?? 'aktif') === 'aktif')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                        Wilayah Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                        Wilayah Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>
                        @else
                        <span class="text-gray-400 text-xs">Data tidak tersedia</span>
                        @endif
                    </td>

                    {{-- Informasi Kegiatan --}}
                    <td class="px-4 py-3 align-top">
                        <div class="space-y-0.5 text-xs">
                            <p class="text-gray-500">KEGIATAN :
                                <span class="text-gray-800 font-medium">{{ $item->kegiatan->nama_kegiatan ?? '-' }}</span>
                            </p>
                            <p class="text-gray-500">PERIODE :
                                <span class="text-gray-800 font-medium">
                                    {{ $item->kegiatan->tanggal_mulai->format('d M Y') ?? '-' }}
                                    s/d
                                    {{ $item->kegiatan->tanggal_selesai->format('d M Y') ?? '-' }}
                                </span>
                            </p>
                            <p class="text-gray-500 mt-1">DIUPLOAD :
                                <span class="text-gray-600">{{ $item->user->name ?? '-' }}</span>
                            </p>
                            <p class="text-gray-500">TGL UPLOAD :
                                <span class="text-gray-600">{{ $item->created_at->format('d M Y') }}</span>
                            </p>
                        </div>
                    </td>


                    {{-- Aksi --}}
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-row gap-1.5 items-center justify-center">

                            {{-- Print --}}
                            <button onclick="printSketsa({{ $item->id }}, '{{ route('file.peta', ['path' => $item->path_file]) }}', '{{ pathinfo($item->path_file, PATHINFO_EXTENSION) }}')"
                                    title="Print"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                            </button>

                            {{-- Edit & Hapus — supervisor saja --}}
                            @if(auth()->user()->isSupervisor())
                            <button onclick='openEditModal(@json($item))'
                                    title="Edit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="openHapusModal({{ $item->id }}, '{{ $item->wilayah->nama_desa ?? '' }}')"
                                    title="Hapus"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <p class="text-gray-400 text-sm">Belum ada data peta {{ $jenis }}</p>
                        @if(auth()->user()->isSupervisor())
                        <button onclick="openTambahModal()"
                                class="mt-3 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Tambah Peta Pertama
                        </button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($peta->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $peta->firstItem() }}–{{ $peta->lastItem() }} dari {{ $peta->total() }} peta
        </p>
        {{ $peta->links() }}
    </div>
    @endif
</div>

{{-- ===== MODAL PREVIEW GAMBAR ===== --}}
<div id="modalPreview" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-80"
     onclick="closePreview()">
    <div class="relative max-w-4xl max-h-screen p-4">
        <img id="previewImg" src="" alt="Preview Peta"
             class="max-w-full max-h-screen object-contain rounded-lg"/>
        <button onclick="closePreview()"
                class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white text-gray-800 flex items-center justify-center hover:bg-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

{{-- ===== MODAL TAMBAH (supervisor) ===== --}}
@if(auth()->user()->isSupervisor())
<div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Tambah Peta {{ $jenis }}</h3>
            <button onclick="closeTambahModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('peta.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jenis_peta" value="{{ $jenis }}"/>
            @if($errors->any())
            <div class="mx-6 mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-xs text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="px-6 py-4 space-y-4">

                    {{-- Pilih Kecamatan --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Kecamatan <span class="text-red-500">*</span>
                        </label>
                        <select id="tambah_kec" onchange="loadDesaTambah(this.value)" 
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($kecamatan as $kec)
                                <option value="{{ $kec->kode_kec }}">
                                    [{{ str_pad($kec->kode_kec, 3, '0', STR_PAD_LEFT) }}] {{ $kec->nama_kec }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilih Desa --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Desa/Kelurahan <span class="text-red-500">*</span>
                        </label>
                        <select id="tambah_desa"
                                onchange="{{ $jenis === 'SLS' ? 'loadSlsTambah(this.value)' : '' }}"
                                @if($jenis === 'WA') name="wilayah_id" required @endif
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Desa --</option>
                        </select>
                    </div>

                    {{-- Pilih SLS — hanya untuk jenis SLS --}}
                    @if($jenis === 'SLS')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">SLS <span class="text-red-500">*</span></label>
                        <select name="wilayah_id" id="tambah_sls_wilayah" required
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih SLS --</option>
                        </select>
                    </div>
                    @endif

                {{-- Kegiatan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kegiatan <span class="text-red-500">*</span></label>
                    <select name="kegiatan_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kegiatan --</option>
                        @foreach($kegiatan as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kegiatan }} ({{ $k->tanggal_mulai->format('Y') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun" required
                            value="{{ old('tahun', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                </div>

                {{-- Upload File --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">File Peta <span class="text-red-500">*</span></label>
                    <input type="file" name="file_peta" required accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, PDF. Maks: 10MB</p>
                </div>

            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="closeTambahModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDIT ===== --}}
@if(auth()->user()->isSupervisor())
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Edit Peta {{ $jenis }}</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="formEdit" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 space-y-4">

                {{-- Info Wilayah (read only) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        @if($jenis === 'WA') Desa/Kelurahan @else SLS @endif
                    </label>
                    <div id="edit_wilayah_display"
                         class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-700 min-h-9">
                    </div>
                    <input type="hidden" name="wilayah_id" id="edit_wilayah_id"/>
                    <p class="text-xs text-gray-400 mt-1">Wilayah tidak dapat diubah setelah peta dibuat.</p>
                </div>

                {{-- Kegiatan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kegiatan <span class="text-red-500">*</span></label>
                    <select name="kegiatan_id" id="edit_kegiatan_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kegiatan --</option>
                        @foreach($kegiatan as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kegiatan }} ({{ $k->tanggal_mulai->format('Y') }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" id="edit_tahun" required
                           min="2000" max="{{ date('Y') + 1 }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>

                {{-- Ganti File --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Ganti File Peta</label>
                    <input type="file" name="file_peta" accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti file</p>
                </div>

            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===== MODAL HAPUS ===== --}}
<div id="modalHapus" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4">
        <div class="px-6 py-5 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Hapus Peta</h3>
            <p class="text-sm text-gray-500">Apakah kamu yakin ingin menghapus peta</p>
            <p class="text-sm font-semibold text-gray-800 my-2" id="hapus_nama"></p>
            <p class="text-xs text-red-500">File peta akan ikut terhapus permanen.</p>
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
@endif


{{-- Scripts --}}
<script>
const JENIS = '{{ $jenis }}';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let currentPrintPetaId = null;
let currentPrintUrl    = '';
let currentPrintExt    = '';

function previewGambar(url) {
    document.getElementById('previewImg').src = url;
    document.getElementById('modalPreview').classList.remove('hidden');
    document.getElementById('modalPreview').classList.add('flex');
}
function closePreview() {
    document.getElementById('modalPreview').classList.add('hidden');
    document.getElementById('modalPreview').classList.remove('flex');
}

function printSketsa(petaId, fileUrl, fileExt) {
    fetch('/peta/' + petaId + '/download', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ aksi: 'print' })
    })
    .then(r => r.json())
    .then(data => { if (data.success) printGambar(fileUrl, fileExt); })
    .catch(() => printGambar(fileUrl, fileExt));
}

function printGambar(fileUrl, fileExt) {
    const ext = fileExt.toLowerCase();
    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none;';
    document.body.appendChild(iframe);
    const doc = iframe.contentWindow.document;

    if (['jpg','jpeg','png'].includes(ext)) {
        doc.open();
        doc.write(`<!DOCTYPE html><html><head><title>Print</title>
            <style>*{margin:0;padding:0;}body{display:flex;justify-content:center;align-items:center;min-height:100vh;}
            img{max-width:100%;max-height:100vh;object-fit:contain;pointer-events:none;}</style>
            </head><body><img src="${fileUrl}"
            onload="window.focus();window.print();"
            oncontextmenu="return false;" ondragstart="return false;"/></body></html>`);
        doc.close();
    } else {
        iframe.src = fileUrl;
        iframe.onload = () => { try { iframe.contentWindow.focus(); iframe.contentWindow.print(); } catch(e) {} };
    }

    setTimeout(() => { if (document.body.contains(iframe)) document.body.removeChild(iframe); }, 30000);
    iframe.contentWindow.onafterprint = () => { if (document.body.contains(iframe)) document.body.removeChild(iframe); };
}

// ===== LOAD DESA (form tambah) =====
function loadDesaTambah(kodeKec) {
    const selectDesa = document.getElementById('tambah_desa');
    const selectSls  = document.getElementById('tambah_sls_wilayah');
    selectDesa.innerHTML = '<option value="">Memuat...</option>';
    if (selectSls) selectSls.innerHTML = '<option value="">-- Pilih SLS --</option>';
    if (!kodeKec) {
        selectDesa.innerHTML = '<option value="">-- Pilih Desa --</option>';
        return;
    }
    fetch(`/api/desa-by-kec?kode_kec=${kodeKec}`)
        .then(r => r.json())
        .then(data => {
            selectDesa.innerHTML = '<option value="">-- Pilih Desa --</option>';
            data.forEach(d => {
                const kodeDes = d.kode_des ? String(d.kode_des).padStart(3, '0') : '-';
                selectDesa.innerHTML += `<option value="${d.kode_des}">[${kodeDes}] ${d.nama_des}</option>`;
            });
        });
}

// ===== LOAD SLS (form tambah — hanya jenis SLS) =====
function loadSlsTambah(kodeDes) {
    if (JENIS !== 'WS') return;
    const select  = document.getElementById('tambah_sls_wilayah');
    const kodeKec = document.getElementById('tambah_kec').value;
    if (!select) return;
    select.innerHTML = '<option value="">Memuat...</option>';
    if (!kodeDes) {
        select.innerHTML = '<option value="">-- Pilih SLS --</option>';
        return;
    }
    fetch(`/api/sls-by-des?kode_kec=${kodeKec}&kode_des=${kodeDes}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Pilih SLS --</option>';
            if (data.length === 0) {
                select.innerHTML = '<option value="">Tidak ada SLS ditemukan</option>';
                return;
            }
            data.forEach(d => {
                const idSubsls   = d.id_subsls   ? d.id_subsls : '-';
                const kodeSls    = d.kode_sls    ? String(d.kode_sls).padStart(4, '0')    : '-';
                const kodeSubsls = d.kode_subsls ? String(d.kode_subsls).padStart(2, '0') : '-';
                const statusTag  = d.status === 'nonaktif' ? ' (Nonaktif)' : '';
                select.innerHTML += `<option value="${d.id}">[${idSubsls}] ${d.nama_sls} — ${kodeSls}/${kodeSubsls}${statusTag}</option>`;
            });
        });
}

// ===== FILTER — desa berubah =====
function filterDesaChange(kodeKec) {
    const selectDesa = document.getElementById('filter_desa');
    const selectSls  = document.getElementById('filter_sls');
    selectDesa.innerHTML = '<option value="">Memuat...</option>';
    if (selectSls) selectSls.innerHTML = '<option value="">-- Semua --</option>';
    if (!kodeKec) {
        selectDesa.innerHTML = '<option value="">-- Semua --</option>';
        return;
    }
    fetch(`/api/desa-by-kec?kode_kec=${kodeKec}`)
        .then(r => r.json())
        .then(data => {
            selectDesa.innerHTML = '<option value="">-- Semua --</option>';
            data.forEach(d => {
                const kodeDes = d.kode_des ? String(d.kode_des).padStart(3, '0') : '-';
                selectDesa.innerHTML += `<option value="${d.kode_des}">[${kodeDes}] ${d.nama_des}</option>`;
            });
        });
}

// ===== FILTER — SLS berubah =====
function filterSlsChange(kodeDes) {
    if (JENIS !== 'WS') return;
    const select  = document.getElementById('filter_sls');
    const kodeKec = document.getElementById('filter_kec').value;
    if (!select) return;
    select.innerHTML = '<option value="">Memuat...</option>';
    if (!kodeDes) {
        select.innerHTML = '<option value="">-- Semua --</option>';
        return;
    }
    fetch(`/api/sls-by-des?kode_kec=${kodeKec}&kode_des=${kodeDes}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Semua --</option>';
            data.forEach(d => {
                const idSubsls  = d.id_subsls ? d.id_subsls : '-';
                const statusTag = d.status === 'nonaktif' ? ' (Nonaktif)' : '';
                select.innerHTML += `<option value="${d.id}">[${idSubsls}] ${d.nama_sls}${statusTag}</option>`;
            });
        });
}

// ===== Restore filter saat load halaman =====
const filterKec = document.getElementById('filter_kec');
if (filterKec && filterKec.value) {
    const kodeDesAktif = '{{ request("kode_des") }}';
    const wilayahAktif = '{{ request("wilayah_id") }}';
    const kodeKecVal   = filterKec.value;

    fetch(`/api/desa-by-kec?kode_kec=${kodeKecVal}`)
        .then(r => r.json())
        .then(data => {
            const selectDesa = document.getElementById('filter_desa');
            selectDesa.innerHTML = '<option value="">-- Semua --</option>';
            data.forEach(d => {
                const kodeDes = d.kode_des ? String(d.kode_des).padStart(3, '0') : '-';
                const sel     = d.kode_des == kodeDesAktif ? 'selected' : '';
                selectDesa.innerHTML += `<option value="${d.kode_des}" ${sel}>[${kodeDes}] ${d.nama_des}</option>`;
            });

            if (kodeDesAktif && JENIS === 'WS') {
                fetch(`/api/sls-by-des?kode_kec=${kodeKecVal}&kode_des=${kodeDesAktif}`)
                    .then(r => r.json())
                    .then(slsData => {
                        const selectSls = document.getElementById('filter_sls');
                        if (!selectSls) return;
                        selectSls.innerHTML = '<option value="">-- Semua --</option>';
                        slsData.forEach(s => {
                            const idSubsls = s.id_subsls ? s.id_subsls : '-';
                            const sel      = s.id == wilayahAktif ? 'selected' : '';
                            selectSls.innerHTML += `<option value="${s.id}" ${sel}>[${idSubsls}] ${s.nama_sls}</option>`;
                        });
                    });
            }
        });
}

function openEditModal(item) {
    const display = document.getElementById('edit_wilayah_display');
    if (display && item.wilayah) {
        if (JENIS === 'WA') {
            const kodeDes = item.wilayah.kode_des || '-';
            const namaDes = item.wilayah.nama_des || '-';
            display.textContent = '[' + kodeDes + '] ' + namaDes;
        } else {
            const namaDes = item.wilayah.nama_des || '-';
            const namaSls = item.wilayah.nama_sls || '-';
            display.textContent = namaDes + ' — ' + namaSls;
        }
    } else if (display) {
        display.textContent = '-';
    }

    document.getElementById('edit_wilayah_id').value  = item.wilayah_id;
    document.getElementById('edit_kegiatan_id').value = item.kegiatan_id;
    document.getElementById('edit_tahun').value       = item.tahun;
    document.getElementById('formEdit').action        = '/peta/' + item.id;
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
    document.getElementById('modalEdit').classList.remove('flex');
}

@if(auth()->user()->isSupervisor())
function openTambahModal() {
    document.getElementById('modalTambah').classList.remove('hidden');
    document.getElementById('modalTambah').classList.add('flex');
}
function closeTambahModal() {
    document.getElementById('modalTambah').classList.add('hidden');
    document.getElementById('modalTambah').classList.remove('flex');
}

function openHapusModal(id, nama) {
    document.getElementById('hapus_nama').textContent = nama + '?';
    document.getElementById('formHapus').action = '/peta/' + id;
    document.getElementById('modalHapus').classList.remove('hidden');
    document.getElementById('modalHapus').classList.add('flex');
}
function closeHapusModal() {
    document.getElementById('modalHapus').classList.add('hidden');
    document.getElementById('modalHapus').classList.remove('flex');
}

@if($errors->any())
    openTambahModal();
@endif
@endif

['modalPreview', 'modalTambah', 'modalEdit', 'modalHapus'].forEach(id => {
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