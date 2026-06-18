<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\Peta;
use App\Models\TransaksiPeta;
use App\Models\User;
use App\Models\Kegiatan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSupervisor()) {

            $totalWilayah         = Wilayah::where('status', 'aktif')->count();
            $totalWilayahNonaktif = Wilayah::where('status', 'nonaktif')->count();
            $totalPeta            = Peta::count();
            $totalPetaWA          = Peta::where('jenis_peta', 'WA')->count();
            $totalPetaSLS         = Peta::where('jenis_peta', 'SLS')->count();
            $totalPengguna        = User::count();
            $totalTransaksiTahun  = TransaksiPeta::whereYear('tanggal', now()->year)->count();
            $totalDipinjam        = TransaksiPeta::where('status', 'dipinjam')->count();
            $totalDikembalikan    = TransaksiPeta::where('status', 'dikembalikan')->count();

            $transaksiTerbaru = TransaksiPeta::with(['peta.wilayah', 'user', 'kegiatan'])
                                              ->orderBy('created_at', 'desc')
                                              ->limit(5)->get();

            $petaTerbaru = Peta::with(['wilayah', 'kegiatan', 'user'])
                               ->orderBy('created_at', 'desc')
                               ->limit(5)->get();

            $kegiatanAktif = Kegiatan::where('tanggal_selesai', '>=', now()->toDateString())
                                      ->orderBy('tanggal_mulai')->get();

            $wilayahPerKec = Wilayah::select('nama_kec')
                                     ->selectRaw('COUNT(*) as total')
                                     ->where('status', 'aktif')
                                     ->whereNotNull('nama_kec')
                                     ->groupBy('nama_kec')
                                     ->orderByDesc('total')
                                     ->get();

            return view('dashboard', compact(
                'totalWilayah', 'totalWilayahNonaktif',
                'totalPeta', 'totalPetaWA', 'totalPetaSLS',
                'totalPengguna', 'totalTransaksiTahun',
                'totalDipinjam', 'totalDikembalikan',
                'transaksiTerbaru', 'petaTerbaru',
                'kegiatanAktif', 'wilayahPerKec'
            ));

        } else {

            $totalPeta             = Peta::count();
            $totalPinjamOperator   = TransaksiPeta::where('user_id', $user->id)
                                                   ->where('status', 'dipinjam')->count();
            $totalKembaliOperator  = TransaksiPeta::where('user_id', $user->id)
                                                   ->where('status', 'dikembalikan')->count();

            $petaTerbaru = Peta::with(['wilayah', 'kegiatan', 'user'])
                               ->orderBy('created_at', 'desc')
                               ->limit(5)->get();

            $kegiatanAktif = Kegiatan::where('tanggal_selesai', '>=', now()->toDateString())
                                      ->orderBy('tanggal_mulai')->get();

            $wilayahPerKec = Wilayah::select('nama_kec')
                                     ->selectRaw('COUNT(*) as total')
                                     ->where('status', 'aktif')
                                     ->whereNotNull('nama_kec')
                                     ->groupBy('nama_kec')
                                     ->orderByDesc('total')
                                     ->get();

            return view('dashboard', compact(
                'totalPeta', 'totalPinjamOperator', 'totalKembaliOperator',
                'petaTerbaru', 'kegiatanAktif', 'wilayahPerKec'
            ));
        }
    }
}