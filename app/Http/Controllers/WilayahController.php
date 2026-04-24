<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Imports\WilayahImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = Wilayah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_sls', 'like', "%{$search}%")
                ->orWhere('nama_kec', 'like', "%{$search}%")
                ->orWhere('nama_des', 'like', "%{$search}%")
                ->orWhere('id_subsls', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kode_kec')) {
            $query->where('kode_kec', $request->kode_kec);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $wilayah    = $query->orderBy('kode_des')->orderBy('kode_sls')->paginate(20)->withQueryString();
        $kecamatan  = Wilayah::select('kode_kec', 'nama_kec')->distinct()->whereNotNull('kode_kec')->orderBy('nama_kec')->get();
        $pilihanIds = session('wilayah_pilihan', []); // ← pastikan baris ini ada

        return view('wilayah.index', compact('wilayah', 'kecamatan', 'pilihanIds')); // ← pastikan pilihanIds ada di compact
    }

    public function show(Wilayah $wilayah)
    {
        $wilayahAsal = $wilayah->wilayahAsal();
        return view('wilayah.show', compact('wilayah', 'wilayahAsal'));
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'nama_sls'    => 'required|string|max:100',
            'nama_ketua'  => 'nullable|string|max:100',
            'jenis'       => 'nullable|string|max:50',
            'kk'          => 'nullable|integer|min:0',
            'btt'         => 'nullable|integer|min:0',
            'bttk'        => 'nullable|integer|min:0',
            'bku'         => 'nullable|integer|min:0',
            'bbtt_nonusaha' => 'nullable|integer|min:0',
            'usaha'       => 'nullable|integer|min:0',
            'muatan'      => 'nullable|integer|min:0',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        $wilayah->update($request->only([
            'nama_sls', 'nama_ketua', 'jenis',
            'kk', 'btt', 'bttk', 'bku',
            'bbtt_nonusaha', 'usaha', 'muatan', 'status',
        ]));

        return redirect()->route('wilayah.show', $wilayah)
                         ->with('success', 'Data wilayah berhasil diperbarui.');
    }

    public function updateStatusAsal(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'asal_id' => 'required|exists:wilayah,id',
            'status'  => 'required|in:aktif,nonaktif',
        ]);

        Wilayah::where('id', $request->asal_id)->update(['status' => $request->status]);

        return redirect()->route('wilayah.show', $wilayah)
                         ->with('success', 'Status wilayah asal berhasil diubah.');
    }

    public function destroy(Wilayah $wilayah)
    {
        $wilayah->delete();
        return redirect()->route('wilayah.index')
                         ->with('success', 'Data wilayah berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_excel.required' => 'File excel wajib diupload.',
            'file_excel.mimes'    => 'Format file harus XLSX, XLS, atau CSV.',
            'file_excel.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            Excel::import(new WilayahImport, $request->file('file_excel'));
            return redirect()->route('wilayah.index')
                             ->with('success', 'Data wilayah berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('wilayah.index')
                             ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function gabung(Request $request)
    {
        $request->validate([
            'wilayah_ids'  => 'required|array|min:2',
            'wilayah_ids.*'=> 'exists:wilayah,id',
            'nama_baru'    => 'required|string|max:100',
            'tipe_gabung'  => 'required|in:baru,ikut_pertama',
            'nama_ketua'   => 'nullable|string|max:100',
            'jenis'        => 'nullable|string|max:50',
        ], [
            'wilayah_ids.required' => 'Pilih minimal 2 wilayah untuk digabung.',
            'wilayah_ids.min'      => 'Pilih minimal 2 wilayah untuk digabung.',
            'nama_baru.required'   => 'Nama wilayah baru wajib diisi.',
            'tipe_gabung.required' => 'Tipe penggabungan wajib dipilih.',
        ]);

        $wilayahDipilih = Wilayah::whereIn('id', $request->wilayah_ids)->get();
        $pertama        = $wilayahDipilih->first();

        // Buat wilayah baru atau ikut pertama
        if ($request->tipe_gabung === 'baru') {
            $wilayahBaru = Wilayah::create([
                'nama_sls'        => $request->nama_baru,
                'nama_ketua'      => $request->nama_ketua,
                'jenis'           => $request->jenis ?? $pertama->jenis,
                'kode_prop'       => $pertama->kode_prop,
                'kode_kab'        => $pertama->kode_kab,
                'kode_kec'        => $pertama->kode_kec,
                'kode_des'        => $pertama->kode_des,
                'kode_sls'        => $pertama->kode_sls,
                'kode_subsls'     => $pertama->kode_subsls,
                'nama_prop'       => $pertama->nama_prop,
                'nama_kab'        => $pertama->nama_kab,
                'nama_kec'        => $pertama->nama_kec,
                'nama_des'        => $pertama->nama_des,
                'kk'              => $wilayahDipilih->sum('kk'),
                'btt'             => $wilayahDipilih->sum('btt'),
                'bttk'            => $wilayahDipilih->sum('bttk'),
                'bku'             => $wilayahDipilih->sum('bku'),
                'bbtt_nonusaha'   => $wilayahDipilih->sum('bbtt_nonusaha'),
                'usaha'           => $wilayahDipilih->sum('usaha'),
                'muatan'          => $wilayahDipilih->sum('muatan'),
                'status'          => 'aktif',
                'asal'            => 'gabung',
                'wilayah_asal_ids'=> $request->wilayah_ids,
            ]);
        } else {
            // Ikut wilayah pertama
            $pertama->update([
                'nama_sls'        => $request->nama_baru,
                'kk'              => $wilayahDipilih->sum('kk'),
                'btt'             => $wilayahDipilih->sum('btt'),
                'bttk'            => $wilayahDipilih->sum('bttk'),
                'bku'             => $wilayahDipilih->sum('bku'),
                'bbtt_nonusaha'   => $wilayahDipilih->sum('bbtt_nonusaha'),
                'usaha'           => $wilayahDipilih->sum('usaha'),
                'muatan'          => $wilayahDipilih->sum('muatan'),
                'asal'            => 'gabung',
                'wilayah_asal_ids'=> $request->wilayah_ids,
            ]);
            $wilayahBaru = $pertama;
        }

        // Nonaktifkan semua wilayah yang digabung
        Wilayah::whereIn('id', $request->wilayah_ids)
               ->where('id', '!=', $wilayahBaru->id)
               ->update([
                   'status'   => 'nonaktif',
                   'induk_id' => $wilayahBaru->id,
               ]);

        return redirect()->route('wilayah.index')
                         ->with('success', 'Wilayah berhasil digabung menjadi ' . $wilayahBaru->nama_sls);
    }

    public function pecah(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'pecahan'          => 'required|array|min:2',
            'pecahan.*.nama'   => 'required|string|max:100',
            'pecahan.*.jenis'  => 'nullable|string|max:50',
            'pecahan.*.ketua'  => 'nullable|string|max:100',
            'pecahan.*.kk'     => 'nullable|integer|min:0',
        ], [
            'pecahan.required'       => 'Minimal 2 pecahan wilayah harus diisi.',
            'pecahan.min'            => 'Minimal 2 pecahan wilayah harus diisi.',
            'pecahan.*.nama.required'=> 'Nama setiap pecahan wajib diisi.',
        ]);

        foreach ($request->pecahan as $pecahan) {
            Wilayah::create([
                'nama_sls'    => $pecahan['nama'],
                'nama_ketua'  => $pecahan['ketua'] ?? null,
                'jenis'       => $pecahan['jenis'] ?? $wilayah->jenis,
                'kode_prop'   => $wilayah->kode_prop,
                'kode_kab'    => $wilayah->kode_kab,
                'kode_kec'    => $wilayah->kode_kec,
                'kode_des'    => $wilayah->kode_des,
                'kode_sls'    => $wilayah->kode_sls,
                'nama_prop'   => $wilayah->nama_prop,
                'nama_kab'    => $wilayah->nama_kab,
                'nama_kec'    => $wilayah->nama_kec,
                'nama_des'    => $wilayah->nama_des,
                'kk'          => $pecahan['kk'] ?? 0,
                'status'      => 'aktif',
                'asal'        => 'pecah',
                'induk_id'    => $wilayah->id,
                'wilayah_asal_ids' => [$wilayah->id],
            ]);
        }

        // Nonaktifkan wilayah asal
        $wilayah->update(['status' => 'nonaktif']);

        return redirect()->route('wilayah.index')
                         ->with('success', $wilayah->nama_sls . ' berhasil dipecah menjadi ' . count($request->pecahan) . ' wilayah baru.');
    }

    public function togglePilihan(Request $request)
    {
        $id      = $request->id;
        $pilihan = session('wilayah_pilihan', []);

        if (in_array($id, $pilihan)) {
            $pilihan = array_values(array_filter($pilihan, fn($v) => $v != $id));
        } else {
            $pilihan[] = $id;
        }

        session(['wilayah_pilihan' => $pilihan]);

        return response()->json([
            'success' => true,
            'total'   => count($pilihan),
            'ids'     => $pilihan,
        ]);
    }

    public function clearPilihan()
    {
        session()->forget('wilayah_pilihan');
        return response()->json(['success' => true]);
    }

    public function getPilihan()
    {
        $pilihan = session('wilayah_pilihan', []);
        $data    = Wilayah::whereIn('id', $pilihan)
                        ->select('id', 'nama_sls', 'nama_des')
                        ->get();
        return response()->json([
            'ids'  => $pilihan,
            'data' => $data,
        ]);
    }
}