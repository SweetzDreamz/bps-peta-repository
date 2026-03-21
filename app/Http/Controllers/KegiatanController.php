<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::orderBy('tahun', 'desc')->get();
        return view('kegiatan.index', compact('kegiatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tahun'         => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tahun.required'         => 'Tahun wajib diisi.',
            'tahun.digits'           => 'Tahun harus 4 digit.',
            'tahun.min'              => 'Tahun minimal 2000.',
            'tahun.max'              => 'Tahun tidak valid.',
        ]);

        Kegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'tahun'         => $request->tahun,
        ]);

        return redirect()->route('kegiatan.index')
                         ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tahun'         => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'tahun.required'         => 'Tahun wajib diisi.',
            'tahun.digits'           => 'Tahun harus 4 digit.',
            'tahun.min'              => 'Tahun minimal 2000.',
            'tahun.max'              => 'Tahun tidak valid.',
        ]);

        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'tahun'         => $request->tahun,
        ]);

        return redirect()->route('kegiatan.index')
                         ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();

        return redirect()->route('kegiatan.index')
                         ->with('success', 'Kegiatan berhasil dihapus.');
    }
}