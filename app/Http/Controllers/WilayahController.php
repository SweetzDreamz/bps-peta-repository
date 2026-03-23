<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index(Request $request)
    {
        $query = Wilayah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_desa', 'like', "%{$search}%")
                  ->orWhere('nama_kec', 'like', "%{$search}%")
                  ->orWhere('kode_desa', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kec')) {
            $query->where('nama_kec', $request->kec);
        }

        $wilayah    = $query->orderBy('kode_desa')->paginate(15)->withQueryString();
        $kecamatan  = Wilayah::select('nama_kec', 'kode_kec')
                             ->distinct()
                             ->orderBy('nama_kec')
                             ->get();

        return view('wilayah.index', compact('wilayah', 'kecamatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prop'  => 'required|string|max:10',
            'kode_kab'   => 'required|string|max:10',
            'kode_kec'   => 'required|string|max:10',
            'kode_desa'  => 'required|string|max:10|unique:wilayah,kode_desa',
            'kode_blok'  => 'nullable|string|max:15',
            'nama_prop'  => 'required|string|max:100',
            'nama_kab'   => 'required|string|max:100',
            'nama_kec'   => 'required|string|max:100',
            'nama_desa'  => 'required|string|max:100',
        ], [
            'kode_prop.required'  => 'Kode provinsi wajib diisi.',
            'kode_kab.required'   => 'Kode kabupaten wajib diisi.',
            'kode_kec.required'   => 'Kode kecamatan wajib diisi.',
            'kode_desa.required'  => 'Kode desa wajib diisi.',
            'kode_desa.unique'    => 'Kode desa sudah terdaftar.',
            'nama_prop.required'  => 'Nama provinsi wajib diisi.',
            'nama_kab.required'   => 'Nama kabupaten wajib diisi.',
            'nama_kec.required'   => 'Nama kecamatan wajib diisi.',
            'nama_desa.required'  => 'Nama desa/kelurahan wajib diisi.',
        ]);

        Wilayah::create($request->only([
            'kode_prop', 'kode_kab', 'kode_kec', 'kode_desa',
            'kode_blok', 'nama_prop', 'nama_kab', 'nama_kec', 'nama_desa',
        ]));

        return redirect()->route('wilayah.index')
                         ->with('success', 'Data wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, Wilayah $wilayah)
    {
        $request->validate([
            'kode_prop'  => 'required|string|max:10',
            'kode_kab'   => 'required|string|max:10',
            'kode_kec'   => 'required|string|max:10',
            'kode_desa'  => 'required|string|max:10|unique:wilayah,kode_desa,' . $wilayah->id,
            'kode_blok'  => 'nullable|string|max:15',
            'nama_prop'  => 'required|string|max:100',
            'nama_kab'   => 'required|string|max:100',
            'nama_kec'   => 'required|string|max:100',
            'nama_desa'  => 'required|string|max:100',
        ], [
            'kode_prop.required'  => 'Kode provinsi wajib diisi.',
            'kode_kab.required'   => 'Kode kabupaten wajib diisi.',
            'kode_kec.required'   => 'Kode kecamatan wajib diisi.',
            'kode_desa.required'  => 'Kode desa wajib diisi.',
            'kode_desa.unique'    => 'Kode desa sudah terdaftar.',
            'nama_prop.required'  => 'Nama provinsi wajib diisi.',
            'nama_kab.required'   => 'Nama kabupaten wajib diisi.',
            'nama_kec.required'   => 'Nama kecamatan wajib diisi.',
            'nama_desa.required'  => 'Nama desa/kelurahan wajib diisi.',
        ]);

        $wilayah->update($request->only([
            'kode_prop', 'kode_kab', 'kode_kec', 'kode_desa',
            'kode_blok', 'nama_prop', 'nama_kab', 'nama_kec', 'nama_desa',
        ]));

        return redirect()->route('wilayah.index')
                         ->with('success', 'Data wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah)
    {
        if ($wilayah->sls()->count() > 0) {
            return redirect()->route('wilayah.index')
                             ->with('error', 'Wilayah tidak dapat dihapus karena masih memiliki data SLS.');
        }

        $wilayah->delete();

        return redirect()->route('wilayah.index')
                         ->with('success', 'Data wilayah berhasil dihapus.');
    }
}