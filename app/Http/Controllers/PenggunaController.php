<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::orderBy('created_at', 'desc')->get();
        return view('pengguna.index', compact('pengguna'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nip'      => 'nullable|string|max:20|unique:users,nip',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:supervisor,operator',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan.',
            'nip.unique'        => 'NIP sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
        ]);

        User::create([
            'name'     => $request->name,
            'nip'      => $request->nip,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('pengguna.index')
                         ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $pengguna)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nip'      => ['nullable', 'string', 'max:20', Rule::unique('users', 'nip')->ignore($pengguna->id)],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:supervisor,operator',
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
            'nip.unique'     => 'NIP sudah digunakan.',
            'password.min'   => 'Kata sandi minimal 6 karakter.',
            'role.required'  => 'Role wajib dipilih.',
        ]);

        $data = [
            'name'  => $request->name,
            'nip'   => $request->nip,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')
                         ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return redirect()->route('pengguna.index')
                             ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $pengguna->delete();

        return redirect()->route('pengguna.index')
                         ->with('success', 'Pengguna berhasil dihapus.');
    }
}