<?php

namespace App\Http\Controllers;

use App\Models\Peta;
use App\Models\Wilayah;
use App\Models\Sls;
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
        $query = Peta::with(['wilayah', 'sls', 'kegiatan', 'user'])
                     ->where('jenis_peta', $jenis);

        // Filter wilayah
        if ($request->filled('kode_kec')) {
            $query->whereHas('wilayah', fn($q) =>
                $q->where('kode_kec', $request->kode_kec));
        }
        if ($request->filled('kode_desa')) {
            $query->whereHas('wilayah', fn($q) =>
                $q->where('kode_desa', $request->kode_desa));
        }
        if ($jenis === 'WB' && $request->filled('kode_blok')) {
            $query->whereHas('wilayah', fn($q) =>
                $q->where('kode_blok', $request->kode_blok));
        }
        if ($jenis === 'SLS' && $request->filled('sls_id')) {
            $query->where('sls_id', $request->sls_id);
        }
        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->kegiatan_id);
        }

        $peta       = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $wilayah    = Wilayah::orderBy('kode_desa')->get();
        $kegiatan = Kegiatan::orderBy('tanggal_mulai', 'desc')->get();
        $kecamatan  = Wilayah::select('kode_kec', 'nama_kec')->distinct()->orderBy('nama_kec')->get();
        $slsList    = $jenis === 'SLS' ? Sls::with('wilayah')->orderBy('nama_sls')->get() : collect();

        return view('sketsa.index', compact('peta', 'wilayah', 'kegiatan', 'kecamatan', 'slsList', 'jenis'));
    }

    public function wa(Request $request)  { return $this->index($request, 'WA'); }
    public function sls(Request $request) { return $this->index($request, 'SLS'); }

    // =====================
    // STORE - Tambah
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'jenis_peta'   => 'required|in:WA,SLS',
            'wilayah_id'   => 'required|exists:wilayah,id',
            'sls_id'       => 'nullable|exists:sls,id',
            'kegiatan_id'  => 'required|exists:kegiatan,id',
            'tahun'        => 'required|digits:4',
            'file_peta'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'wilayah_id.required'  => 'Wilayah wajib dipilih.',
            'kegiatan_id.required' => 'Kegiatan wajib dipilih.',
            'tahun.required'       => 'Tahun wajib diisi.',
            'file_peta.required'   => 'File peta wajib diupload.',
            'file_peta.mimes'      => 'Format file harus JPG, PNG, atau PDF.',
            'file_peta.max'        => 'Ukuran file maksimal 10MB.',
        ]);

        $path = $request->file('file_peta')->store('peta/' . strtolower($request->jenis_peta), 'public');

        Peta::create([
            'jenis_peta'  => $request->jenis_peta,
            'wilayah_id'  => $request->wilayah_id,
            'sls_id'      => $request->sls_id,
            'kegiatan_id' => $request->kegiatan_id,
            'tahun'       => $request->tahun,
            'path_file'   => $path,
            'user_id'     => auth()->id(),
        ]);

        $route = 'sketsa.' . strtolower($request->jenis_peta);
        return redirect()->route($route)->with('success', 'Sketsa peta berhasil ditambahkan.');
    }

    // =====================
    // UPDATE - Edit
    // =====================
    public function update(Request $request, Peta $peta)
    {
        $request->validate([
            'wilayah_id'   => 'required|exists:wilayah,id',
            'sls_id'       => 'nullable|exists:sls,id',
            'kegiatan_id'  => 'required|exists:kegiatan,id',
            'tahun'        => 'required|digits:4',
            'file_peta'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'wilayah_id.required'  => 'Wilayah wajib dipilih.',
            'kegiatan_id.required' => 'Kegiatan wajib dipilih.',
            'file_peta.mimes'      => 'Format file harus JPG, PNG, atau PDF.',
            'file_peta.max'        => 'Ukuran file maksimal 10MB.',
        ]);

        if ($request->hasFile('file_peta')) {
            Storage::disk('public')->delete($peta->path_file);
            $path = $request->file('file_peta')->store('peta/' . strtolower($peta->jenis_peta), 'public');
            $peta->path_file = $path;
        }

        $peta->update([
            'wilayah_id'  => $request->wilayah_id,
            'sls_id'      => $request->sls_id,
            'kegiatan_id' => $request->kegiatan_id,
            'tahun'       => $request->tahun,
            'path_file'   => $peta->path_file,
        ]);

        $route = 'sketsa.' . strtolower($peta->jenis_peta);
        return redirect()->route($route)->with('success', 'Sketsa peta berhasil diperbarui.');
    }

    // =====================
    // DESTROY - Hapus
    // =====================
    public function destroy(Peta $peta)
    {
        Storage::disk('public')->delete($peta->path_file);
        $jenis = $peta->jenis_peta;
        $peta->delete();

        $route = 'sketsa.' . strtolower($jenis);
        return redirect()->route($route)->with('success', 'Sketsa peta berhasil dihapus.');
    }

    // =====================
    // DOWNLOAD / PRINT
    // =====================
    public function download(Request $request, Peta $peta)
    {
        $request->validate([
            'aksi' => 'required|in:download,print',
        ]);

        // Ambil kegiatan_id langsung dari peta
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
    // AJAX - Get desa by kec
    // =====================
    public function getDesaByKec(Request $request)
    {
        $desa = Wilayah::where('kode_kec', $request->kode_kec)
                       ->select('id', 'kode_desa', 'nama_desa', 'kode_blok')
                       ->orderBy('nama_desa')
                       ->get();
        return response()->json($desa);
    }

    // =====================
    // AJAX - Get SLS by wilayah
    // =====================
    public function getSlsByWilayah(Request $request)
    {
        $sls = Sls::where('wilayah_id', $request->wilayah_id)
                  ->select('id', 'kode_sls', 'kode_sub_sls', 'nama_sls')
                  ->orderBy('kode_sls')
                  ->get();
        return response()->json($sls);
    }
}