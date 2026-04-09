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
        $query = TransaksiPeta::with(['peta.wilayah', 'peta.sls', 'user', 'kegiatan'])
                              ->orderBy('created_at', 'desc');

        // Operator hanya lihat milik sendiri
        if ($user->isOperator()) {
            $query->where('user_id', $user->id);
        }

        // Filter jenis peta
        if ($request->filled('jenis_peta')) {
            $query->whereHas('peta', fn($q) =>
                $q->where('jenis_peta', $request->jenis_peta));
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kecamatan
        if ($request->filled('kode_kec')) {
            $query->whereHas('peta.wilayah', fn($q) =>
                $q->where('kode_kec', $request->kode_kec));
        }

        // Filter desa
        if ($request->filled('kode_desa')) {
            $query->whereHas('peta.wilayah', fn($q) =>
                $q->where('kode_desa', $request->kode_desa));
        }

        // Filter kegiatan
        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->kegiatan_id);
        }

        // Filter tanggal
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $history   = $query->paginate(15)->withQueryString();
        $kegiatan  = Kegiatan::orderBy('tahun', 'desc')->get();
        $kecamatan = Wilayah::select('kode_kec', 'nama_kec')->distinct()->orderBy('nama_kec')->get();

        // Stats
        $totalDipinjam     = TransaksiPeta::when($user->isOperator(), fn($q) => $q->where('user_id', $user->id))
                                          ->where('status', 'dipinjam')->count();
        $totalDikembalikan = TransaksiPeta::when($user->isOperator(), fn($q) => $q->where('user_id', $user->id))
                                          ->where('status', 'dikembalikan')->count();
        $totalSemua        = TransaksiPeta::when($user->isOperator(), fn($q) => $q->where('user_id', $user->id))
                                          ->count();

        return view('history.index', compact(
            'history', 'kegiatan', 'kecamatan',
            'totalDipinjam', 'totalDikembalikan', 'totalSemua'
        ));
    }

    public function kembalikan(Request $request, TransaksiPeta $transaksi)
    {
        // Pastikan operator hanya bisa kembalikan milik sendiri
        if (auth()->user()->isOperator() && $transaksi->user_id !== auth()->id()) {
            return redirect()->route('history.index')
                            ->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'file_kembali' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'file_kembali.required' => 'File peta yang diperbarui wajib diupload.',
            'file_kembali.mimes'    => 'Format file harus JPG, PNG, atau PDF.',
            'file_kembali.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        $petaLama  = $transaksi->peta;
        $jenisPeta = strtolower($petaLama->jenis_peta ?? 'wa');

        // Simpan file yang dikembalikan ke folder peta (bukan folder kembali)
        $path = $request->file('file_kembali')
                        ->store("peta/{$jenisPeta}", 'public');

        // Buat entri peta baru dari file yang dikembalikan operator
        $petaBaru = Peta::create([
            'jenis_peta'  => $petaLama->jenis_peta,
            'wilayah_id'  => $petaLama->wilayah_id,
            'sls_id'      => $petaLama->sls_id,
            'kegiatan_id' => $transaksi->kegiatan_id,
            'tahun'       => $petaLama->tahun,
            'path_file'   => $path,
            'user_id'     => auth()->id(),
        ]);

        // Update status transaksi menjadi dikembalikan
        $transaksi->update([
            'status'        => 'dikembalikan',
            'file_kembali'  => $path,
            'waktu_kembali' => now(),
        ]);

        return redirect()->route('history.index')
                        ->with('success', 'Peta berhasil dikembalikan dan masuk sebagai peta baru di Management Sketsa.');
    }

    public function batalkan(TransaksiPeta $transaksi)
{
    // Hanya supervisor yang bisa batalkan
    if (auth()->user()->isOperator()) {
        return redirect()->route('history.index')
                         ->with('error', 'Akses ditolak.');
    }

    $transaksi->delete();

    return redirect()->route('history.index')
                     ->with('success', 'Peminjaman berhasil dibatalkan.');
}
}