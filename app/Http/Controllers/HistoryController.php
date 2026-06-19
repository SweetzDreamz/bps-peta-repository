<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPeta;
use App\Models\Kegiatan;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Peta;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = TransaksiPeta::with(['peta.wilayah', 'user', 'kegiatan'])
                              ->orderBy('created_at', 'desc');

        if ($user->isOperator()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('jenis_peta')) {
            $query->whereHas('peta', fn($q) =>
                $q->where('jenis_peta', $request->jenis_peta));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kode_kec')) {
            $query->whereHas('peta.wilayah', fn($q) =>
                $q->where('kode_kec', $request->kode_kec));
        }

        if ($request->filled('kode_des')) {
            $query->whereHas('peta.wilayah', fn($q) =>
                $q->where('kode_des', $request->kode_des)
                  ->where('kode_kec', $request->kode_kec));
        }

        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->kegiatan_id);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $history = $query->paginate(15)->withQueryString();


        $totalDipinjam     = TransaksiPeta::when($user->isOperator(), fn($q) =>
                                $q->where('user_id', $user->id))
                                ->where('status', 'dipinjam')->count();

        $totalDikembalikan = TransaksiPeta::when($user->isOperator(), fn($q) =>
                                $q->where('user_id', $user->id))
                                ->where('status', 'dikembalikan')->count();

        $totalSemua        = TransaksiPeta::when($user->isOperator(), fn($q) =>
                                $q->where('user_id', $user->id))
                                ->count();

        $kegiatan  = Kegiatan::orderBy('tanggal_mulai', 'desc')->get();

        $kecamatan = Wilayah::select('kode_kec', 'nama_kec')
                            ->distinct()
                            ->whereNotNull('kode_kec')
                            ->whereNotNull('nama_kec')
                            ->orderByRaw('CAST(kode_kec AS UNSIGNED)')
                            ->get();

        $desa = collect();
        if ($request->filled('kode_kec')) {
            $desa = Wilayah::select('kode_des', 'nama_des', 'kode_kec')
                           ->distinct()
                           ->where('kode_kec', $request->kode_kec)
                           ->whereNotNull('kode_des')
                           ->whereNotNull('nama_des')
                           ->orderBy('kode_des')
                           ->get();
        }

        return view('history.index', compact(
            'history', 'kegiatan', 'kecamatan', 'desa',
            'totalDipinjam', 'totalDikembalikan', 'totalSemua'
        ));
    }

    public function kembalikan(Request $request, TransaksiPeta $transaksi)
    {
        if (auth()->user()->isOperator() && $transaksi->user_id !== auth()->id()) {
            return redirect()->route('riwayat.index')
                             ->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'file_kembali' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'file_kembali.required' => 'File peta yang diperbarui wajib diupload.',
            'file_kembali.mimes'    => 'Format file tidak didukung. Hanya JPG, PNG, dan PDF yang diizinkan.',
            'file_kembali.max'      => 'Ukuran file terlalu besar. Maksimal 10MB.',
        ]);

        $petaLama  = $transaksi->peta;
        $jenisPeta = strtolower($petaLama->jenis_peta ?? 'wa');

        $path = $request->file('file_kembali')
                        ->store("peta/{$jenisPeta}", 'public');

        Peta::create([
            'jenis_peta'  => $petaLama->jenis_peta,
            'wilayah_id'  => $petaLama->wilayah_id,
            'kegiatan_id' => $transaksi->kegiatan_id,
            'tahun'       => $petaLama->tahun,
            'path_file'   => $path,
            'user_id'     => auth()->id(),
        ]);

        $transaksi->update([
            'status'        => 'dikembalikan',
            'file_kembali'  => $path,
            'waktu_kembali' => now(),
        ]);

        return redirect()->route('riwayat.index')
                         ->with('success', 'Peta berhasil dikembalikan dan masuk sebagai peta baru.');
    }

    public function batalkan(TransaksiPeta $transaksi)
    {
        if (auth()->user()->isOperator()) {
            return redirect()->route('riwayat.index')
                             ->with('error', 'Akses ditolak.');
        }

        $transaksi->delete();

        return redirect()->route('riwayat.index')
                         ->with('success', 'Peminjaman berhasil dibatalkan.');
    }
}