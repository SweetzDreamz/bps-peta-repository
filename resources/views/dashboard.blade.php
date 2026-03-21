@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Total Wilayah SLS</p>
        <h3 class="text-3xl font-bold text-gray-800">0</h3>
        <p class="text-xs text-blue-500 mt-2">📁 Belum ada data</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Total File Peta</p>
        <h3 class="text-3xl font-bold text-gray-800">0</h3>
        <p class="text-xs text-green-500 mt-2">🗺️ Belum ada data</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Transaksi Bulan Ini</p>
        <h3 class="text-3xl font-bold text-gray-800">0</h3>
        <p class="text-xs text-orange-500 mt-2">📋 Belum ada transaksi</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Total Pengguna</p>
        <h3 class="text-3xl font-bold text-gray-800">1</h3>
        <p class="text-xs text-purple-500 mt-2">👤 Admin aktif</p>
    </div>

</div>

{{-- Info Panel --}}
<div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h4 class="text-lg font-semibold text-gray-700 mb-2">Selamat Datang di Sistem Arsip Peta BPS Kota Bogor</h4>
    <p class="text-sm text-gray-500">Sistem ini digunakan untuk mengelola arsip peta SLS, riwayat perubahan wilayah, dan transaksi distribusi peta kepada petugas lapangan.</p>
</div>

@endsection