@extends('layouts.app')

@section('page-title', 'Sketsa ' . $jenis)

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
            {{-- Tab WA / WB / SLS --}}
            <a href="{{ route('sketsa.wa') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                      {{ $jenis === 'WA' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                WA
            </a>
            <a href="{{ route('sketsa.wb') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                      {{ $jenis === 'WB' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                WB
            </a>
            <a href="{{ route('sketsa.sls') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                      {{ $jenis === 'SLS' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                SLS
            </a>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Management Sketsa — {{ $jenis }}</h3>
        <p class="text-sm text-gray-500 mt-1">
            @if($jenis === 'WA') Sketsa Wilayah Administrasi
            @elseif($jenis === 'WB') Sketsa Wilayah Blok Sensus
            @else Sketsa Satuan Lingkungan Setempat
            @endif
        </p>
    </div>
    @if(auth()->user()->isSupervisor())
    <button onclick="openTambahModal()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Sketsa
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
                        {{ $kec->nama_kec }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Desa --}}
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Desa/Kelurahan</label>
            <select name="kode_desa" id="filter_desa"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
            </select>
        </div>

        {{-- Filter Blok Sensus (khusus WB) --}}
        @if($jenis === 'WB')
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Kode Blok</label>
            <input type="text" name="kode_blok" value="{{ request('kode_blok') }}"
                   placeholder="Contoh: 001"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        @endif

        {{-- Filter SLS (khusus SLS) --}}
        @if($jenis === 'SLS')
        <div class="flex-1 min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">SLS</label>
            <select name="sls_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua --</option>
                @foreach($slsList as $sls)
                    <option value="{{ $sls->id }}" {{ request('sls_id') == $sls->id ? 'selected' : '' }}>
                        {{ $sls->nama_sls }} ({{ $sls->wilayah->nama_desa ?? '-' }})
                    </option>
                @endforeach
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
                        {{ $k->nama_kegiatan }} ({{ $k->tahun }})
                    </option>
                @endforeach
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
        <p class="text-sm font-semibold text-gray-700">Daftar Sketsa {{ $jenis }}</p>
        <p class="text-xs text-gray-400">{{ $peta->total() }} sketsa ditemukan</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-12">No</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-32">Sketsa</th>
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

                    {{-- Thumbnail Sketsa --}}
                    <td class="px-4 py-3 align-top">
                        @php $ext = pathinfo($item->path_file, PATHINFO_EXTENSION); @endphp
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                            <img src="{{ Storage::url($item->path_file) }}"
                                 alt="Sketsa {{ $jenis }}"
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
                        <div class="space-y-0.5 text-xs">
                            <p class="text-gray-500">PROVINSI :
                                <span class="text-gray-800 font-medium">
                                    [{{ $item->wilayah->kode_prop ?? '-' }}] {{ $item->wilayah->nama_prop ?? '-' }}
                                </span>
                            </p>
                            <p class="text-gray-500">KAB/KOTA :
                                <span class="text-gray-800 font-medium">
                                    [{{ $item->wilayah->kode_kab ?? '-' }}] {{ $item->wilayah->nama_kab ?? '-' }}
                                </span>
                            </p>
                            <p class="text-gray-500">KECAMATAN :
                                <span class="text-gray-800 font-medium">
                                    [{{ $item->wilayah->kode_kec ?? '-' }}] {{ $item->wilayah->nama_kec ?? '-' }}
                                </span>
                            </p>
                            <p class="text-gray-500">DESA :
                                <span class="text-gray-800 font-medium">
                                    [{{ $item->wilayah->kode_desa ?? '-' }}] {{ $item->wilayah->nama_desa ?? '-' }}
                                </span>
                            </p>
                            @if($jenis === 'WB' && $item->wilayah->kode_blok)
                            <p class="text-gray-500">BLOK :
                                <span class="text-gray-800 font-medium font-mono">{{ $item->wilayah->kode_blok }}</span>
                            </p>
                            @endif
                            @if($jenis === 'SLS' && $item->sls)
                            <p class="text-gray-500">SLS :
                                <span class="text-gray-800 font-medium">
                                    [{{ $item->sls->kode_sls }}] {{ $item->sls->nama_sls }}
                                </span>
                            </p>
                            @if($item->sls->kode_sub_sls)
                            <p class="text-gray-500">SUB SLS :
                                <span class="text-gray-800 font-medium font-mono">{{ $item->sls->kode_sub_sls }}</span>
                            </p>
                            @endif
                            @endif
                        </div>
                    </td>

                    {{-- Informasi Kegiatan --}}
                    <td class="px-4 py-3 align-top">
                        <div class="space-y-0.5 text-xs">
                            <p class="text-gray-500">KEGIATAN :
                                <span class="text-gray-800 font-medium">{{ $item->kegiatan->nama_kegiatan ?? '-' }}</span>
                            </p>
                            <p class="text-gray-500">TAHUN :
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $item->tahun }}
                                </span>
                            </p>
                            <p class="text-gray-500 mt-1">DIUPLOAD :
                                <span class="text-gray-600">{{ $item->user->name ?? '-' }}</span>
                            </p>
                            <p class="text-gray-500">TGL :
                                <span class="text-gray-600">{{ $item->created_at->format('d M Y') }}</span>
                            </p>
                        </div>
                    </td>


                    {{-- Aksi --}}
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-col gap-1.5 items-center">

                            {{-- Download --}}
                            <button onclick="openDownloadModal({{ $item->id }}, 'download')"
                                    title="Download"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>

                            {{-- Print --}}
                            <button onclick="openPrintModal({{ $item->id }}, '{{ Storage::url($item->path_file) }}', '{{ pathinfo($item->path_file, PATHINFO_EXTENSION) }}')"
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
                        <p class="text-gray-400 text-sm">Belum ada data sketsa {{ $jenis }}</p>
                        @if(auth()->user()->isSupervisor())
                        <button onclick="openTambahModal()"
                                class="mt-3 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Tambah Sketsa Pertama
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
            Menampilkan {{ $peta->firstItem() }}–{{ $peta->lastItem() }} dari {{ $peta->total() }} sketsa
        </p>
        {{ $peta->links() }}
    </div>
    @endif
</div>

{{-- ===== MODAL PREVIEW GAMBAR ===== --}}
<div id="modalPreview" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-80"
     onclick="closePreview()">
    <div class="relative max-w-4xl max-h-screen p-4">
        <img id="previewImg" src="" alt="Preview Sketsa"
             class="max-w-full max-h-screen object-contain rounded-lg"/>
        <button onclick="closePreview()"
                class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white text-gray-800 flex items-center justify-center hover:bg-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

{{-- ===== MODAL DOWNLOAD/PRINT ===== --}}
<div id="modalDownload" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800" id="downloadModalTitle">Download Sketsa</h3>
            <button onclick="closeDownloadModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="formDownload">
            @csrf
            <input type="hidden" name="aksi" id="input_aksi" value="download"/>
            <div class="px-6 py-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Pilih Kegiatan <span class="text-red-500">*</span>
                </label>
                <select name="kegiatan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kegiatan --</option>
                    @foreach($kegiatan as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kegiatan }} ({{ $k->tahun }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-2">
                    Aksi ini akan tercatat di riwayat transaksi.
                </p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeDownloadModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" id="btnDownloadSubmit"
                        class="px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    Download
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL PRINT ===== --}}
<div id="modalPrint" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Print Sketsa</h3>
            <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Pilih Kegiatan <span class="text-red-500">*</span>
                </label>
                <select id="print_kegiatan_id" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kegiatan --</option>
                    @foreach($kegiatan as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kegiatan }} ({{ $k->tahun }})</option>
                    @endforeach
                </select>
            </div>
            <p class="text-xs text-gray-400">
                Pilih kegiatan terlebih dahulu. Aksi print ini akan tercatat di riwayat transaksi.
            </p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
            <button type="button" onclick="closePrintModal()"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                Batal
            </button>
            <button type="button" onclick="submitPrint()"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Sekarang
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH (supervisor) ===== --}}
@if(auth()->user()->isSupervisor())
<div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Tambah Sketsa {{ $jenis }}</h3>
            <button onclick="closeTambahModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('sketsa.store') }}" enctype="multipart/form-data">
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
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kecamatan <span class="text-red-500">*</span></label>
                    <select id="tambah_kec" onchange="loadDesaTambah(this.value)"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatan as $kec)
                            <option value="{{ $kec->kode_kec }}">{{ $kec->nama_kec }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilih Desa → wilayah_id --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Desa/Kelurahan <span class="text-red-500">*</span></label>
                    <select name="wilayah_id" id="tambah_desa" required
                            @if($jenis === 'SLS') onchange="loadSlsTambah(this.value)" @endif
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Desa --</option>
                    </select>
                </div>

                {{-- Pilih SLS (khusus SLS) --}}
                @if($jenis === 'SLS')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">SLS <span class="text-red-500">*</span></label>
                    <select name="sls_id" id="tambah_sls" required
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
                            <option value="{{ $k->id }}">{{ $k->nama_kegiatan }} ({{ $k->tahun }})</option>
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
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Edit Sketsa {{ $jenis }}</h3>
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

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Wilayah <span class="text-red-500">*</span></label>
                    <select name="wilayah_id" id="edit_wilayah_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Wilayah --</option>
                        @foreach($wilayah as $w)
                            <option value="{{ $w->id }}">
                                [{{ $w->kode_desa }}] {{ $w->nama_desa }} - {{ $w->nama_kec }}
                                @if($w->kode_blok) (Blok: {{ $w->kode_blok }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($jenis === 'SLS')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">SLS</label>
                    <select name="sls_id" id="edit_sls_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih SLS --</option>
                        @foreach($slsList as $sls)
                            <option value="{{ $sls->id }}">[{{ $sls->kode_sls }}] {{ $sls->nama_sls }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kegiatan <span class="text-red-500">*</span></label>
                    <select name="kegiatan_id" id="edit_kegiatan_id" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kegiatan --</option>
                        @foreach($kegiatan as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kegiatan }} ({{ $k->tahun }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun" id="edit_tahun" required
                            min="2000" max="{{ date('Y') + 1 }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                </div>

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
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Hapus Sketsa</h3>
            <p class="text-sm text-gray-500">Apakah kamu yakin ingin menghapus sketsa</p>
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
let currentPrintPetaId = null;
let currentPrintUrl    = '';
let currentPrintExt    = '';

// ===== PREVIEW GAMBAR =====
function previewGambar(url) {
    document.getElementById('previewImg').src = url;
    document.getElementById('modalPreview').classList.remove('hidden');
    document.getElementById('modalPreview').classList.add('flex');
}
function closePreview() {
    document.getElementById('modalPreview').classList.add('hidden');
    document.getElementById('modalPreview').classList.remove('flex');
}

// ===== MODAL DOWNLOAD =====
function openDownloadModal(petaId, aksi) {
    document.getElementById('input_aksi').value = aksi;
    document.getElementById('formDownload').action = '/sketsa/' + petaId + '/download';
    document.getElementById('downloadModalTitle').textContent = 'Download Sketsa';
    document.getElementById('btnDownloadSubmit').textContent = 'Download';
    document.getElementById('modalDownload').classList.remove('hidden');
    document.getElementById('modalDownload').classList.add('flex');
}
function closeDownloadModal() {
    document.getElementById('modalDownload').classList.add('hidden');
    document.getElementById('modalDownload').classList.remove('flex');
}

// ===== MODAL PRINT =====
function openPrintModal(petaId, fileUrl, fileExt) {
    currentPrintPetaId = petaId;
    currentPrintUrl    = fileUrl;
    currentPrintExt    = fileExt;
    document.getElementById('print_kegiatan_id').value = '';
    document.getElementById('modalPrint').classList.remove('hidden');
    document.getElementById('modalPrint').classList.add('flex');
}
function closePrintModal() {
    document.getElementById('modalPrint').classList.add('hidden');
    document.getElementById('modalPrint').classList.remove('flex');
}
function submitPrint() {
    const kegiatanId = document.getElementById('print_kegiatan_id').value;
    if (!kegiatanId) {
        alert('Pilih kegiatan terlebih dahulu.');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Catat transaksi via fetch
    fetch('/sketsa/' + currentPrintPetaId + '/download', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            aksi: 'print',
            kegiatan_id: kegiatanId,
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closePrintModal();
            // Buka dialog print Windows
            if (['jpg', 'jpeg', 'png'].includes(currentPrintExt.toLowerCase())) {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Print Sketsa</title>
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: white; }
                            img { max-width: 100%; max-height: 100vh; object-fit: contain; }
                        </style>
                    </head>
                    <body>
                        <img src="${currentPrintUrl}"
                             onload="window.focus(); window.print();"
                             onerror="document.body.innerHTML='<p>Gagal memuat gambar</p>';" />
                    </body>
                    </html>
                `);
                printWindow.document.close();
            } else {
                // PDF — buka di tab baru
                const printWindow = window.open(currentPrintUrl, '_blank');
                if (printWindow) {
                    printWindow.addEventListener('load', () => {
                        printWindow.focus();
                        printWindow.print();
                    });
                }
            }
        }
    })
    .catch(() => {
        alert('Gagal mencatat transaksi. Silakan coba lagi.');
    });
}

@if(auth()->user()->isSupervisor())
// ===== MODAL TAMBAH =====
function openTambahModal() {
    document.getElementById('modalTambah').classList.remove('hidden');
    document.getElementById('modalTambah').classList.add('flex');
}
function closeTambahModal() {
    document.getElementById('modalTambah').classList.add('hidden');
    document.getElementById('modalTambah').classList.remove('flex');
}

// Load desa saat pilih kecamatan (form tambah)
function loadDesaTambah(kodeKec) {
    const select = document.getElementById('tambah_desa');
    select.innerHTML = '<option value="">Memuat...</option>';
    if (!kodeKec) { select.innerHTML = '<option value="">-- Pilih Desa --</option>'; return; }
    fetch(`/api/desa-by-kec?kode_kec=${kodeKec}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Pilih Desa --</option>';
            data.forEach(d => {
                const label = d.kode_blok
                    ? `[${d.kode_desa}] ${d.nama_desa} (Blok: ${d.kode_blok})`
                    : `[${d.kode_desa}] ${d.nama_desa}`;
                select.innerHTML += `<option value="${d.id}">${label}</option>`;
            });
        });
}

// Load SLS saat pilih desa (khusus SLS)
function loadSlsTambah(wilayahId) {
    if (JENIS !== 'SLS') return;
    const select = document.getElementById('tambah_sls');
    if (!select) return;
    select.innerHTML = '<option value="">Memuat...</option>';
    if (!wilayahId) { select.innerHTML = '<option value="">-- Pilih SLS --</option>'; return; }
    fetch(`/api/sls-by-wilayah?wilayah_id=${wilayahId}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Pilih SLS --</option>';
            data.forEach(s => {
                const label = s.kode_sub_sls
                    ? `[${s.kode_sls}] ${s.nama_sls} (Sub: ${s.kode_sub_sls})`
                    : `[${s.kode_sls}] ${s.nama_sls}`;
                select.innerHTML += `<option value="${s.id}">${label}</option>`;
            });
        });
}

// ===== MODAL EDIT =====
function openEditModal(item) {
    document.getElementById('edit_wilayah_id').value  = item.wilayah_id;
    document.getElementById('edit_kegiatan_id').value = item.kegiatan_id;
    document.getElementById('edit_tahun').value       = item.tahun;
    if (JENIS === 'SLS') {
        document.getElementById('edit_sls_id').value = item.sls_id ?? '';
    }
    document.getElementById('formEdit').action = '/sketsa/' + item.id;
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
    document.getElementById('modalEdit').classList.remove('flex');
}

// ===== MODAL HAPUS =====
function openHapusModal(id, nama) {
    document.getElementById('hapus_nama').textContent = nama + '?';
    document.getElementById('formHapus').action = '/sketsa/' + id;
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

// Tutup semua modal klik luar
['modalDownload', 'modalPrint', 'modalPreview', 'modalTambah', 'modalEdit', 'modalHapus'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });
});

// Filter desa saat load halaman
const filterKec = document.getElementById('filter_kec');
if (filterKec && filterKec.value) {
    const kodeDesaAktif = '{{ request("kode_desa") }}';
    fetch(`/api/desa-by-kec?kode_kec=${filterKec.value}`)
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('filter_desa');
            select.innerHTML = '<option value="">-- Semua --</option>';
            data.forEach(d => {
                const label = d.kode_blok
                    ? `[${d.kode_desa}] ${d.nama_desa} (Blok: ${d.kode_blok})`
                    : `[${d.kode_desa}] ${d.nama_desa}`;
                const selected = d.kode_desa === kodeDesaAktif ? 'selected' : '';
                select.innerHTML += `<option value="${d.kode_desa}" ${selected}>${label}</option>`;
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
                const label = d.kode_blok
                    ? `[${d.kode_desa}] ${d.nama_desa} (Blok: ${d.kode_blok})`
                    : `[${d.kode_desa}] ${d.nama_desa}`;
                select.innerHTML += `<option value="${d.kode_desa}">${label}</option>`;
            });
        });
}
</script>

@endsection