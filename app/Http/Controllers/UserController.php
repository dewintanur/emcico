<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    // Menampilkan daftar user
    public function index()
    {
        $users = User::all();
        return view('it.userList', compact('users'));
    }

    // Menampilkan form tambah user
    public function create()
    {
        return view('it.tambahUser');
    }

    // Menyimpan user ke database
    public function store(Request $request)
    {
        \Log::info('Menerima request untuk tambah user', $request->all());

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:it,marketing,produksi,front_office,duty_officer', // Sesuai ENUM di database
            'password' => 'required|string|min:6'
        ]);

        \Log::info('Request berhasil divalidasi');

        try {
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password), // Enkripsi password
            ]);

            \Log::info('User berhasil ditambahkan', ['user_id' => $user->id]);

            return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Gagal menambahkan user', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menambahkan user.');
        }
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('it.editUser', compact('user'));
    }
    
    public function update(Request $request, $id)
    {
        \Log::info('Menerima request update user', $request->all());
    
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id, // Cegah duplikasi email kecuali email yang sedang diedit
            'role' => 'required|in:it,marketing,produksi,front_office,duty_officer', // Sesuai ENUM di database
        ]);
    
        \Log::info('Request valid');
    
        try {
            $user = User::findOrFail($id);
            $user->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'role' => $request->role,
            ]);
    
            \Log::info('User berhasil diperbarui', ['user_id' => $user->id]);
    
            return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Gagal memperbarui user', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui user.');
        }
    }
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
    
            return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus user', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat menghapus user.');
        }
    }
    
}

