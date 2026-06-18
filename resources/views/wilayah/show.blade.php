@extends('layouts.app')

@section('page-title', 'Detail Wilayah')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- Banner perlu edit --}}
@if($wilayah->perlu_edit)
<div class="mb-4 px-4 py-3 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <span class="text-sm font-medium">Wilayah ini hasil dari proses <strong>{{ $wilayah->asal }}</strong> dan belum diedit. Harap periksa dan lengkapi atribut yang kosong, lalu simpan untuk menghapus penanda ini.</span>
</div>
@endif

{{-- Header --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('wilayah.index') }}"
       class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h3 class="text-xl font-bold text-gray-800">{{ $wilayah->nama_sls }}</h3>
        <p class="text-sm text-gray-500 mt-0.5">Detail & Pengelolaan Wilayah SLS</p>
    </div>
    <div class="ml-auto">
        @if($wilayah->status === 'aktif')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Aktif</span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">Nonaktif</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Form Edit Wilayah --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h4 class="text-sm font-semibold text-gray-700">Atribut Wilayah</h4>
        </div>
        <form method="POST" action="{{ route('wilayah.update', $wilayah) }}">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 grid grid-cols-2 gap-4">

                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama SLS <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sls" required value="{{ $wilayah->nama_sls }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Ketua</label>
                    <input type="text" name="nama_ketua" value="{{ $wilayah->nama_ketua }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jenis</label>
                    <input type="text" name="jenis" value="{{ $wilayah->jenis }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Status Wilayah</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="status" value="aktif"
                                   {{ $wilayah->status === 'aktif' ? 'checked' : '' }}
                                   class="accent-green-600"/>
                            <span class="text-green-700 font-medium">Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="status" value="nonaktif"
                                   {{ $wilayah->status === 'nonaktif' ? 'checked' : '' }}
                                   class="accent-gray-600"/>
                            <span class="text-gray-600 font-medium">Nonaktif</span>
                        </label>
                    </div>
                </div>

                <p class="col-span-2 text-xs font-semibold text-gray-400 uppercase tracking-wide pt-2">Data Statistik</p>

                @foreach([['kk','KK'],['btt','BTT'],['bttk','BTTK'],['bku','BKU'],['bbtt_nonusaha','BBtt Non Usaha'],['usaha','Usaha'],['muatan','Muatan']] as [$field, $label])
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                    <input type="number" name="{{ $field }}" min="0" value="{{ $wilayah->$field ?? 0 }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                @endforeach

            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Info & Wilayah Asal --}}
    <div class="space-y-4">

        {{-- Info Kode Wilayah --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Informasi Kode Wilayah</h4>
            <div class="space-y-2 text-xs">
                @foreach([
                    ['ID SubSLS', $wilayah->id_subsls],
                    ['Kode Prop', $wilayah->kode_prop],
                    ['Kode Kab', $wilayah->kode_kab],
                    ['Kode Kec', $wilayah->kode_kec],
                    ['Kode Des', $wilayah->kode_des],
                    ['Kode SLS', $wilayah->kode_sls],
                    ['Kode SubSLS', $wilayah->kode_subsls],
                    ['Klas', $wilayah->klas],
                    ['Asal Data', ucfirst($wilayah->asal)],
                ] as [$label, $value])
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ $label }}</span>
                    <span class="font-mono font-medium text-gray-800">{{ $value ?? '-' }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Wilayah Asal (jika hasil gabung/pecah) --}}
        @if($wilayah->asal !== 'import' && count($wilayahAsal) > 0)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">
                Wilayah Asal
                <span class="text-xs font-normal text-gray-400 ml-1">({{ ucfirst($wilayah->asal) }})</span>
            </h4>
            <div class="space-y-2">
                @foreach($wilayahAsal as $asal)
                <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-gray-100">
                    <div>
                        <p class="text-xs font-medium text-gray-800">{{ $asal->nama_sls }}</p>
                        <p class="text-xs text-gray-400">{{ $asal->nama_des }} — {{ $asal->nama_kec }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs {{ $asal->status === 'aktif' ? 'text-green-600' : 'text-gray-400' }}">
                            {{ ucfirst($asal->status) }}
                        </span>
                        {{-- Toggle status asal --}}
                        <form method="POST" action="{{ route('wilayah.status-asal', $wilayah) }}">
                            @csrf
                            <input type="hidden" name="asal_id" value="{{ $asal->id }}"/>
                            <input type="hidden" name="status" value="{{ $asal->status === 'aktif' ? 'nonaktif' : 'aktif' }}"/>
                            <button type="submit"
                                    title="Toggle Status"
                                    class="w-6 h-6 flex items-center justify-center rounded text-xs
                                           {{ $asal->status === 'aktif' ? 'bg-gray-100 text-gray-500 hover:bg-gray-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }}
                                           transition-colors">
                                @if($asal->status === 'aktif')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection