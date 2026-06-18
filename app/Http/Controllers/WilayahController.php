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
                ->orWhere('nama_ketua', 'like', "%{$search}%")
                ->orWhere('nama_kec', 'like', "%{$search}%")
                ->orWhere('nama_des', 'like', "%{$search}%")
                ->orWhere('id_subsls', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kode_kec')) {
            $query->where('kode_kec', $request->kode_kec);
        }

        if ($request->filled('kode_des')) {
            $query->where('kode_des', $request->kode_des)
                ->where('kode_kec', $request->kode_kec);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $wilayah = $query->orderBy('id_subsls')->paginate(20)->withQueryString();

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

        $pilihanIds = session('wilayah_pilihan', []);

        return view('wilayah.index', compact('wilayah', 'kecamatan', 'desa', 'pilihanIds'));
    }

    public function show(Wilayah $wilayah)
    {
        $wilayahAsal = $wilayah->wilayahAsal();
        return view('wilayah.show', compact('wilayah', 'wilayahAsal'));
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'nama_sls'      => 'required|string|max:100',
            'nama_ketua'    => 'nullable|string|max:100',
            'jenis'         => 'nullable|string|max:50',
            'kk'            => 'nullable|integer|min:0',
            'btt'           => 'nullable|integer|min:0',
            'bttk'          => 'nullable|integer|min:0',
            'bku'           => 'nullable|integer|min:0',
            'bbtt_nonusaha' => 'nullable|integer|min:0',
            'usaha'         => 'nullable|integer|min:0',
            'muatan'        => 'nullable|integer|min:0',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        $wilayah->update(array_merge(
            $request->only([
                'nama_sls', 'nama_ketua', 'jenis',
                'kk', 'btt', 'bttk', 'bku',
                'bbtt_nonusaha', 'usaha', 'muatan', 'status',
            ]),
            ['perlu_edit' => false] // hapus flag setelah disimpan
        ));

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
            'wilayah_ids'      => 'required|array|min:2',
            'wilayah_ids.*'    => 'exists:wilayah,id',
            'tipe_gabung'      => 'required|in:baru,pilih_aktif',
            'nama_baru'        => 'required_if:tipe_gabung,baru|nullable|string|max:100',
            'id_subsls_baru'   => 'required_if:tipe_gabung,baru|nullable|string|max:20',
            'wilayah_aktif_id' => 'required_if:tipe_gabung,pilih_aktif|nullable|exists:wilayah,id',
        ]);

        $ids            = array_map('intval', $request->wilayah_ids);
        $wilayahDipilih = Wilayah::whereIn('id', $ids)->get();

        if ($wilayahDipilih->count() < 2) {
            return redirect()->route('wilayah.index')
                            ->with('error', 'Minimal 2 wilayah harus dipilih untuk digabung.');
        }

        $pertama = $wilayahDipilih->first();

        // Hitung total statistik
        $totalStats = [
            'kk'            => $wilayahDipilih->sum('kk'),
            'btt'           => $wilayahDipilih->sum('btt'),
            'bttk'          => $wilayahDipilih->sum('bttk'),
            'bku'           => $wilayahDipilih->sum('bku'),
            'bbtt_nonusaha' => $wilayahDipilih->sum('bbtt_nonusaha'),
            'usaha'         => $wilayahDipilih->sum('usaha'),
            'muatan'        => $wilayahDipilih->sum('muatan'),
        ];

        if ($request->tipe_gabung === 'baru') {

            // Buat wilayah baru
            $wilayahBaru = Wilayah::create(array_merge($totalStats, [
                'id_subsls'        => $request->id_subsls_baru,
                'nama_sls'         => $request->nama_baru,
                'kode_subsls'      => $request->kode_subsls_baru ?? null,
                'jenis'            => $pertama->jenis,
                'kode_prop'        => $pertama->kode_prop,
                'kode_kab'         => $pertama->kode_kab,
                'kode_kec'         => $pertama->kode_kec,
                'kode_des'         => $pertama->kode_des,
                'kode_sls'         => $pertama->kode_sls,
                'nama_prop'        => $pertama->nama_prop,
                'nama_kab'         => $pertama->nama_kab,
                'nama_kec'         => $pertama->nama_kec,
                'nama_des'         => $pertama->nama_des,
                'status'           => 'aktif',
                'asal'             => 'gabung',
                'wilayah_asal_ids' => $ids,
                'perlu_edit'       => true,
            ]));

            // Nonaktifkan SEMUA wilayah yang dipilih
            Wilayah::whereIn('id', $ids)->update([
                'status'   => 'nonaktif',
                'induk_id' => $wilayahBaru->id,
            ]);

        } else {

            // Pilih wilayah aktif yang tetap
            $aktifId      = intval($request->wilayah_aktif_id);
            $wilayahAktif = Wilayah::findOrFail($aktifId);

            // Update wilayah yang dipilih sebagai aktif
            $wilayahAktif->update(array_merge($totalStats, [
                'asal'             => 'gabung',
                'wilayah_asal_ids' => $ids,
                'perlu_edit'       => true,
            ]));

            // Nonaktifkan semua KECUALI yang terpilih sebagai aktif
            Wilayah::whereIn('id', $ids)
                ->where('id', '!=', $aktifId)
                ->update([
                    'status'   => 'nonaktif',
                    'induk_id' => $aktifId,
                ]);
        }

        session()->forget('wilayah_pilihan');

        return redirect()->route('wilayah.index')
                        ->with('success', 'Wilayah berhasil digabung.');
    }

    public function pecah(Request $request)
    {
        $request->validate([
            'wilayah_id'           => 'required|exists:wilayah,id',
            'pecahan'              => 'required|array|min:2',
            'pecahan.*.id_subsls'  => 'required|string|max:20',
            'pecahan.*.nama'       => 'required|string|max:100',
            'pecahan.*.kode_subsls'=> 'nullable|string|max:2',
            'pecahan.*.aktif'      => 'nullable',
        ]);

        $wilayah = Wilayah::findOrFail($request->wilayah_id);

        foreach ($request->pecahan as $pecahan) {
            $isAktif = isset($pecahan['aktif']) && $pecahan['aktif'] == '1';
            Wilayah::create([
                'id_subsls'        => $pecahan['id_subsls'],
                'nama_sls'         => $pecahan['nama'],
                'kode_subsls'      => $pecahan['kode_subsls'] ?? null,
                'jenis'            => $wilayah->jenis,
                'kode_prop'        => $wilayah->kode_prop,
                'kode_kab'         => $wilayah->kode_kab,
                'kode_kec'         => $wilayah->kode_kec,
                'kode_des'         => $wilayah->kode_des,
                'kode_sls'         => $wilayah->kode_sls,
                'nama_prop'        => $wilayah->nama_prop,
                'nama_kab'         => $wilayah->nama_kab,
                'nama_kec'         => $wilayah->nama_kec,
                'nama_des'         => $wilayah->nama_des,
                'kk'               => $wilayah->kk,
                'status'           => $isAktif ? 'aktif' : 'nonaktif',
                'asal'             => 'pecah',
                'induk_id'         => $wilayah->id,
                'wilayah_asal_ids' => [$wilayah->id],
                'perlu_edit'       => true,
            ]);
        }

        $wilayah->update(['status' => 'nonaktif']);

        session()->forget('wilayah_pilihan');

        return redirect()->route('wilayah.index')
                        ->with('success', $wilayah->nama_sls . ' berhasil dipecah menjadi ' . count($request->pecahan) . ' wilayah.');
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

    public function getDesaByKec(Request $request)
    {
        $desa = Wilayah::select('kode_des', 'nama_des', 'kode_kec')
                    ->distinct()
                    ->where('kode_kec', $request->kode_kec)
                    ->whereNotNull('kode_des')
                    ->whereNotNull('nama_des')
                    ->orderBy('kode_des')
                    ->get();

        return response()->json($desa);
    }
}