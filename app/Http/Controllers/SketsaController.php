<?php

namespace App\Http\Controllers;

use App\Models\Peta;
use App\Models\Wilayah;
use App\Models\Kegiatan;
use App\Models\TransaksiPeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SketsaController extends Controller
{
    // =====================
    // INDEX - Tampil daftar
    // =====================
    private function index(Request $request, string $jenis)
    {
        $query = Peta::with(['wilayah', 'kegiatan', 'user'])
                     ->where('jenis_peta', $jenis);

        if (!$request->filled('status_wilayah')) {
            $query->whereHas('wilayah', fn($q) => $q->where('status', 'aktif'));
        } elseif ($request->status_wilayah !== 'semua') {
            $query->whereHas('wilayah', fn($q) =>
                $q->where('status', $request->status_wilayah));
        }

        if ($request->filled('kode_kec')) {
            $query->whereHas('wilayah', fn($q) =>
                $q->where('kode_kec', $request->kode_kec));
        }

        if ($request->filled('kode_des')) {
            $query->whereHas('wilayah', fn($q) =>
                $q->where('kode_des', $request->kode_des));
        }

        if ($request->filled('wilayah_id')) {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->kegiatan_id);
        }

        $peta      = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $kegiatan  = Kegiatan::orderBy('tanggal_mulai', 'desc')->get();
        $kecamatan = Wilayah::select('kode_kec', 'nama_kec')
                            ->distinct()
                            ->whereNotNull('kode_kec')
                            ->orderByRaw('CAST(kode_kec AS UNSIGNED)')
                            ->get();

        return view('sketsa.index', compact(
            'peta', 'kegiatan', 'kecamatan', 'jenis'
        ));
    }

    public function wa(Request $request)  { return $this->index($request, 'WA'); }
    public function ws(Request $request)  { return $this->index($request, 'WS'); }

    // =====================
    // STORE - Tambah
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'jenis_peta'  => 'required|in:WA,WS',
            'wilayah_id'  => 'required|exists:wilayah,id',
            'kegiatan_id' => 'required|exists:kegiatan,id',
            'tahun'       => 'required|digits:4',
            'file_peta'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'wilayah_id.required'  => 'Wilayah wajib dipilih.',
            'kegiatan_id.required' => 'Kegiatan wajib dipilih.',
            'tahun.required'       => 'Tahun wajib diisi.',
            'file_peta.required'   => 'File peta wajib diupload.',
            'file_peta.mimes'      => 'Format file harus JPG, PNG, atau PDF.',
            'file_peta.max'        => 'Ukuran file maksimal 10MB.',
        ]);

        $path = $request->file('file_peta')
                        ->store('peta/' . strtolower($request->jenis_peta), 'public');

        Peta::create([
            'jenis_peta'  => $request->jenis_peta,
            'wilayah_id'  => $request->wilayah_id,
            'kegiatan_id' => $request->kegiatan_id,
            'tahun'       => $request->tahun,
            'path_file'   => $path,
            'user_id'     => auth()->id(),
        ]);

        $route = $request->jenis_peta === 'WA' ? 'peta.wa' : 'peta.ws';
        return redirect()->route($route)->with('success', 'Peta berhasil ditambahkan.');
    }

    // =====================
    // UPDATE - Edit
    // =====================
    public function update(Request $request, Peta $peta)
    {
        $request->validate([
            'wilayah_id'  => 'required|exists:wilayah,id',
            'kegiatan_id' => 'required|exists:kegiatan,id',
            'tahun'       => 'required|digits:4',
            'file_peta'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'wilayah_id.required'  => 'Wilayah wajib dipilih.',
            'kegiatan_id.required' => 'Kegiatan wajib dipilih.',
            'file_peta.mimes'      => 'Format file harus JPG, PNG, atau PDF.',
            'file_peta.max'        => 'Ukuran file maksimal 10MB.',
        ]);

        if ($request->hasFile('file_peta')) {
            Storage::disk('public')->delete($peta->path_file);
            $path = $request->file('file_peta')
                            ->store('peta/' . strtolower($peta->jenis_peta), 'public');
            $peta->path_file = $path;
        }

        $peta->update([
            'wilayah_id'  => $request->wilayah_id,
            'kegiatan_id' => $request->kegiatan_id,
            'tahun'       => $request->tahun,
            'path_file'   => $peta->path_file,
        ]);

        $route = $peta->jenis_peta === 'WA' ? 'peta.wa' : 'peta.ws';
        return redirect()->route($route)->with('success', 'Peta berhasil diperbarui.');
    }

    // =====================
    // DESTROY - Hapus
    // =====================
    public function destroy(Peta $peta)
    {
        Storage::disk('public')->delete($peta->path_file);
        $jenis = $peta->jenis_peta;
        $peta->delete();

        $route = $jenis === 'WA' ? 'peta.wa' : 'peta.ws';
        return redirect()->route($route)->with('success', 'Peta berhasil dihapus.');
    }

    // =====================
    // DOWNLOAD / PRINT
    // =====================
    public function download(Request $request, Peta $peta)
    {
        $request->validate([
            'aksi' => 'required|in:download,print',
        ]);

        TransaksiPeta::create([
            'peta_id'     => $peta->id,
            'user_id'     => auth()->id(),
            'kegiatan_id' => $peta->kegiatan_id,
            'tanggal'     => now()->toDateString(),
            'status'      => 'dipinjam',
        ]);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true]);
        }

        $filePath = Storage::disk('public')->path($peta->path_file);
        $fileName = basename($peta->path_file);

        return response()->download($filePath, $fileName);
    }

    // =====================
    // AJAX
    // =====================
    public function getDesaByKec(Request $request)
    {
        $desa = Wilayah::where('kode_kec', $request->kode_kec)
                    ->select('kode_kec', 'kode_des', 'nama_des')
                    ->distinct()
                    ->whereNotNull('kode_des')
                    ->whereNotNull('nama_des')
                    ->orderBy('kode_des')
                    ->get();

        return response()->json($desa);
    }

    public function getSlsByDes(Request $request)
    {
        $query = Wilayah::select('id', 'id_subsls', 'kode_kec', 'kode_des',
                                'kode_sls', 'kode_subsls', 'nama_sls',
                                'nama_kec', 'nama_des', 'status')
                        ->orderBy('kode_sls')
                        ->orderBy('kode_subsls')
                        ->orderBy('nama_sls');

        // Filter wajib pakai KOMBINASI kode_kec + kode_des
        if ($request->filled('kode_kec') && $request->filled('kode_des')) {
            $query->where('kode_kec', $request->kode_kec)
                ->where('kode_des', $request->kode_des);
        } elseif ($request->filled('kode_des')) {
            $query->where('kode_des', $request->kode_des);
        }

        if (!$request->filled('semua')) {
            $query->where('status', 'aktif');
        }

        return response()->json($query->get());
    }

    public function getAllSls(Request $request)
    {
        $sls = Wilayah::select('id', 'id_subsls', 'kode_sls', 'kode_subsls', 'nama_sls', 'nama_des', 'nama_kec', 'status')
                      ->where('status', 'aktif')
                      ->orderBy('nama_kec')
                      ->orderBy('nama_des')
                      ->orderBy('nama_sls')
                      ->get();

        return response()->json($sls);
    }
}