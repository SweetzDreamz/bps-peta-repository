@extends('layouts.app')

@section('page-title', 'Data Wilayah')

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
        <h3 class="text-xl font-bold text-gray-800">Data Wilayah</h3>
        <p class="text-sm text-gray-500 mt-1">Daftar wilayah administratif BPS Kota Bogor</p>
    </div>
    <button onclick="openTambahModal()"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Wilayah
    </button>
</div>

{{-- Filter & Tabel --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- Filter --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <form method="GET" action="{{ route('wilayah.index') }}" class="flex items-center gap-3 flex-wrap">
            <div class="relative flex-1 min-w-48">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari kode atau nama desa/kecamatan..."
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <select name="kec"
                    class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua Kecamatan --</option>
                @foreach($kecamatan as $kec)
                    <option value="{{ $kec->nama_kec }}" {{ request('kec') == $kec->nama_kec ? 'selected' : '' }}>
                        {{ $kec->nama_kec }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Filter
            </button>
            @if(request('search') || request('kec'))
            <a href="{{ route('wilayah.index') }}"
               class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-12">No</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode Desa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Desa/Kelurahan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kecamatan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kab/Kota</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode Blok</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($wilayah as $index => $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ ($wilayah->currentPage() - 1) * $wilayah->perPage() + $index + 1 }}
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $item->kode_desa }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->nama_desa }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->nama_kec }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->nama_kab }}</td>
                    <td class="px-4 py-3">
                        @if($item->kode_blok)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 font-mono">
                                {{ $item->kode_blok }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick='openEditModal(@json($item))'
                                    title="Edit"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="openHapusModal({{ $item->id }}, '{{ $item->nama_desa }}')"
                                    title="Hapus"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <p class="text-sm">Belum ada data wilayah</p>
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
        {{ $wilayah->links('vendor.pagination.simple') }}
    </div>
    @else
    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
        <p class="text-xs text-gray-500">Menampilkan {{ $wilayah->total() }} wilayah</p>
    </div>
    @endif

</div>

{{-- MODAL TAMBAH --}}
<div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Tambah Data Wilayah</h3>
            <button onclick="closeTambahModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('wilayah.store') }}">
            @csrf
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

                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kode Wilayah</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Provinsi <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_prop" required maxlength="10"
                               value="{{ old('kode_prop', '32') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                               placeholder="Contoh: 32"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Kab/Kota <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_kab" required maxlength="10"
                               value="{{ old('kode_kab', '3271') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                               placeholder="Contoh: 3271"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Kecamatan <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_kec" required maxlength="10"
                               value="{{ old('kode_kec') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                               placeholder="Contoh: 3271010"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Desa/Kelurahan <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_desa" required maxlength="10"
                               value="{{ old('kode_desa') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                               placeholder="Contoh: 3271010001"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kode Blok Sensus</label>
                    <input type="text" name="kode_blok" maxlength="15"
                           value="{{ old('kode_blok') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                           placeholder="Opsional — isi jika data ini adalah Blok Sensus (WB)"/>
                </div>

                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pt-2">Nama Wilayah</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Provinsi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_prop" required maxlength="100"
                               value="{{ old('nama_prop', 'Jawa Barat') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Jawa Barat"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kab/Kota <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kab" required maxlength="100"
                               value="{{ old('nama_kab', 'Kota Bogor') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Kota Bogor"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kecamatan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kec" required maxlength="100"
                               value="{{ old('nama_kec') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Bogor Selatan"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Desa/Kelurahan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_desa" required maxlength="100"
                               value="{{ old('nama_desa') }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Muarasari"/>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="closeTambahModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
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

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Edit Data Wilayah</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="formEdit">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 space-y-4">

                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kode Wilayah</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Provinsi <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_prop" id="edit_kode_prop" required maxlength="10"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Kab/Kota <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_kab" id="edit_kode_kab" required maxlength="10"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Kecamatan <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_kec" id="edit_kode_kec" required maxlength="10"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Desa/Kelurahan <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_desa" id="edit_kode_desa" required maxlength="10"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"/>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kode Blok Sensus</label>
                    <input type="text" name="kode_blok" id="edit_kode_blok" maxlength="15"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                           placeholder="Opsional"/>
                </div>

                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide pt-2">Nama Wilayah</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Provinsi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_prop" id="edit_nama_prop" required maxlength="100"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kab/Kota <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kab" id="edit_nama_kab" required maxlength="100"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Kecamatan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kec" id="edit_nama_kec" required maxlength="100"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Desa/Kelurahan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_desa" id="edit_nama_desa" required maxlength="100"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
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
            <p class="text-sm text-gray-500 mb-1">Apakah kamu yakin ingin menghapus</p>
            <p class="text-sm font-semibold text-gray-800 mb-4" id="hapus_nama"></p>
            <p class="text-xs text-red-500">Wilayah yang memiliki data SLS tidak dapat dihapus.</p>
        </div>
        <div class="px-6 pb-5 flex gap-3">
            <button onclick="closeHapusModal()"
                    class="flex-1 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <form id="formHapus" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
function openTambahModal() {
    document.getElementById('modalTambah').classList.remove('hidden');
    document.getElementById('modalTambah').classList.add('flex');
}
function closeTambahModal() {
    document.getElementById('modalTambah').classList.add('hidden');
    document.getElementById('modalTambah').classList.remove('flex');
}

function openEditModal(item) {
    document.getElementById('edit_kode_prop').value  = item.kode_prop;
    document.getElementById('edit_kode_kab').value   = item.kode_kab;
    document.getElementById('edit_kode_kec').value   = item.kode_kec;
    document.getElementById('edit_kode_desa').value  = item.kode_desa;
    document.getElementById('edit_kode_blok').value  = item.kode_blok ?? '';
    document.getElementById('edit_nama_prop').value  = item.nama_prop;
    document.getElementById('edit_nama_kab').value   = item.nama_kab;
    document.getElementById('edit_nama_kec').value   = item.nama_kec;
    document.getElementById('edit_nama_desa').value  = item.nama_desa;
    document.getElementById('formEdit').action       = '/wilayah/' + item.id;
    document.getElementById('modalEdit').classList.remove('hidden');
    document.getElementById('modalEdit').classList.add('flex');
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
    document.getElementById('modalEdit').classList.remove('flex');
}

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

['modalTambah', 'modalEdit', 'modalHapus'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.classList.remove('flex');
        }
    });
});

@if($errors->any())
    openTambahModal();
@endif
</script>

@endsection