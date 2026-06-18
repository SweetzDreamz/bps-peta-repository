@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')

{{-- Greeting --}}
<div class="mb-6">
    <h3 class="text-xl font-bold text-gray-800">
        Selamat datang, {{ auth()->user()->name }}!
    </h3>
    <p class="text-sm text-gray-500 mt-0.5">
        {{ now()->locale('id')->translatedFormat('l, d F Y') }} —
        @if(auth()->user()->isSupervisor())
            <span class="text-green-600 font-medium">Supervisor</span>
        @else
            <span class="text-blue-600 font-medium">Operator</span>
        @endif
    </p>
</div>

@if(auth()->user()->isSupervisor())

{{-- ===== SUPERVISOR: Baris 1 — 3 card ===== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-4">

    <a href="{{ route('wilayah.index') }}"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-blue-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Wilayah SLS</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalWilayah) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($totalWilayahNonaktif) }} nonaktif</p>
        </div>
    </a>

    <a href="{{ route('peta.wa') }}"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-green-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Peta</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalPeta) }}</p>
            <p class="text-xs text-gray-400 mt-1">WA: {{ $totalPetaWA }} · SLS: {{ $totalPetaSLS }}</p>
        </div>
    </a>

    <a href="{{ route('pengguna.index') }}"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-purple-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Pengguna</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalPengguna) }}</p>
            <p class="text-xs text-gray-400 mt-1">Supervisor &amp; Operator</p>
        </div>
    </a>

</div>

{{-- ===== SUPERVISOR: Baris 2 — 3 card ===== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

    <a href="{{ route('riwayat.index') }}"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-orange-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Transaksi Tahun Ini</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalTransaksiTahun) }}</p>
            <p class="text-xs text-gray-400 mt-1">Tahun {{ now()->year }}</p>
        </div>
    </a>

    <a href="{{ route('riwayat.index') }}?status=dipinjam"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-yellow-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-yellow-50 group-hover:bg-yellow-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Sedang Dipinjam</p>
            <p class="text-3xl font-bold text-orange-500 mt-1">{{ number_format($totalDipinjam) }}</p>
            <p class="text-xs text-gray-400 mt-1">Belum dikembalikan</p>
        </div>
    </a>

    <a href="{{ route('riwayat.index') }}?status=dikembalikan"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-green-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Dikembalikan</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($totalDikembalikan) }}</p>
            <p class="text-xs text-gray-400 mt-1">Selesai dikembalikan</p>
        </div>
    </a>

</div>

@else

{{-- ===== OPERATOR: 3 card ===== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

    <a href="{{ route('peta.wa') }}"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-green-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Peta Tersedia</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalPeta) }}</p>
            <p class="text-xs text-gray-400 mt-1">Dapat diakses</p>
        </div>
    </a>

    <a href="{{ route('riwayat.index') }}?status=dipinjam"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-orange-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Sedang Saya Pinjam</p>
            <p class="text-3xl font-bold text-orange-500 mt-1">{{ number_format($totalPinjamOperator) }}</p>
            <p class="text-xs text-gray-400 mt-1">Belum dikembalikan</p>
        </div>
    </a>

    <a href="{{ route('riwayat.index') }}?status=dikembalikan"
       class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4 hover:shadow-md hover:border-green-200 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center shrink-0 transition-colors">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Telah Dikembalikan</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($totalKembaliOperator) }}</p>
            <p class="text-xs text-gray-400 mt-1">Selesai dikembalikan</p>
        </div>
    </a>

</div>

@endif

{{-- ===== BERSAMA: Transaksi Terbaru (supervisor only) + Kegiatan ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    @if(auth()->user()->isSupervisor())
    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700">Transaksi Terbaru</h4>
            <a href="{{ route('riwayat.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($transaksiTerbaru as $t)
            <div class="px-5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ strtoupper(substr($t->user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-800 truncate">
                        {{ $t->user->name ?? '-' }}
                        <span class="font-normal text-gray-500">meminjam peta</span>
                        {{ $t->peta->wilayah->nama_sls ?? ($t->peta->wilayah->nama_des ?? '-') }}
                    </p>
                    <p class="text-xs text-gray-400">
                        {{ $t->kegiatan->nama_kegiatan ?? '-' }} ·
                        {{ \Carbon\Carbon::parse($t->tanggal)->locale('id')->translatedFormat('d F Y') }}
                    </p>
                </div>
                @if($t->status === 'dipinjam')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 shrink-0">Dipinjam</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 shrink-0">Kembali</span>
                @endif
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">Belum ada transaksi</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <div class="{{ auth()->user()->isSupervisor() ? '' : 'xl:col-span-2' }} bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700">Kegiatan Berlangsung</h4>
            @if(auth()->user()->isSupervisor())
            <a href="{{ route('kegiatan.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Kelola →</a>
            @endif
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($kegiatanAktif as $k)
            <div class="px-5 py-3">
                <p class="text-xs font-medium text-gray-800">{{ $k->nama_kegiatan }}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ \Carbon\Carbon::parse($k->tanggal_mulai)->locale('id')->translatedFormat('d F Y') }}
                    —
                    {{ \Carbon\Carbon::parse($k->tanggal_selesai)->locale('id')->translatedFormat('d F Y') }}
                </p>
                @php
                    $mulai      = \Carbon\Carbon::parse($k->tanggal_mulai)->startOfDay();
                    $selesai    = \Carbon\Carbon::parse($k->tanggal_selesai)->startOfDay();
                    $hari_ini   = now()->startOfDay();
                    $totalHari  = max(1, $mulai->diffInDays($selesai) + 1);
                    $hariLewat  = max(0, min($mulai->diffInDays($hari_ini), $totalHari));
                    $progress   = min(100, (int) round(($hariLewat / $totalHari) * 100));
                    $sisaHari   = (int) $hari_ini->diffInDays($selesai, false);
                @endphp
                <div class="mt-2">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>{{ $progress }}%</span>
                        <span>
                            @if($sisaHari > 0)
                                {{ $sisaHari }} hari lagi
                            @elseif($sisaHari === 0)
                                Berakhir hari ini
                            @else
                                Sudah berakhir
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $progress >= 80 ? 'bg-red-400' : ($progress >= 50 ? 'bg-orange-400' : 'bg-blue-500') }}"
                             style="width:{{ $progress }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">Tidak ada kegiatan berlangsung</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ===== BERSAMA: Peta Terbaru + Distribusi Wilayah ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700">Peta Terbaru Diupload</h4>
            <a href="{{ route('peta.wa') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($petaTerbaru as $p)
            <div class="px-5 py-3 flex items-center gap-3">
                @php $ext = pathinfo($p->path_file, PATHINFO_EXTENSION); @endphp
                @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                <img src="{{ route('file.peta', ['path' => $p->path_file]) }}"
                     alt="Peta" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shrink-0"
                     oncontextmenu="return false;" ondragstart="return false;"/>
                @else
                <div class="w-10 h-10 rounded-lg bg-red-50 border border-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold
                            {{ $p->jenis_peta === 'WA' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $p->jenis_peta }}
                        </span>
                        <p class="text-xs font-medium text-gray-800 truncate">
                            {{ $p->wilayah->nama_sls ?? ($p->wilayah->nama_des ?? '-') }}
                        </p>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $p->kegiatan->nama_kegiatan ?? '-' }} ·
                        {{ \Carbon\Carbon::parse($p->created_at)->locale('id')->translatedFormat('d F Y') }}
                    </p>
                </div>
                <span class="text-xs text-gray-400 shrink-0">{{ $p->user->name ?? '-' }}</span>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-gray-400">Belum ada peta diupload</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h4 class="text-sm font-semibold text-gray-700">Distribusi SLS per Kecamatan</h4>
        </div>
        <div class="px-5 py-4 space-y-3 overflow-y-auto" style="max-height:320px">
            @php $maxTotal = $wilayahPerKec->max('total') ?: 1; @endphp
            @forelse($wilayahPerKec as $w)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <p class="text-xs font-medium text-gray-700 truncate max-w-48">{{ $w->nama_kec }}</p>
                    <span class="text-xs font-bold text-gray-600 shrink-0 ml-2">{{ number_format($w->total) }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full bg-blue-500"
                         style="width:{{ round(($w->total/$maxTotal)*100) }}%"></div>
                </div>
            </div>
            @empty
            <div class="py-8 text-center">
                <p class="text-sm text-gray-400">Belum ada data wilayah</p>
            </div>
            @endforelse
        </div>
        @if($wilayahPerKec->count() > 0)
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 grid grid-cols-3 gap-3">
            <div class="text-center">
                <p class="text-xs text-gray-500">Aktif</p>
                <p class="text-sm font-bold text-green-600">{{ number_format($wilayahPerKec->sum('total')) }}</p>
            </div>
            <div class="text-center border-x border-gray-200">
                <p class="text-xs text-gray-500">Kecamatan</p>
                <p class="text-sm font-bold text-blue-600">{{ $wilayahPerKec->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500">Rata-rata</p>
                <p class="text-sm font-bold text-gray-700">
                    {{ $wilayahPerKec->count() > 0 ? number_format($wilayahPerKec->sum('total') / $wilayahPerKec->count(), 0) : 0 }}
                </p>
            </div>
        </div>
        @endif
    </div>

</div>

@endsection