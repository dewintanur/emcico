<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\LogAktivitas;
class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $logAktivitas = LogAktivitas::where('user_id', $user->id)
                        ->orderBy('waktu', 'desc')
                        ->limit(5) // Tampilkan 5 log terakhir
                        ->get();
    
        return view('profile.index', compact('user', 'logAktivitas'));
    }
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        Log::info('Update profile dimulai untuk user: ' . $user->id);
        Log::info('Data request diterima:', $request->all());
    
        // Validasi Input
        try {
            Log::info('Validasi mulai untuk user: ' . $user->id);
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240' // Maks 10MB
            ]);
    
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'min:6|confirmed',
                ]);
            }
    
            Log::info('Validasi berhasil untuk user: ' . $user->id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi gagal: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
        }
    
        // Update Data
        try {
            Log::info('Memperbarui data user: ' . $user->id);
            $user->nama = $request->nama;
            $user->email = $request->email;
    
            if ($request->password) {
                Log::info('Memperbarui password untuk user: ' . $user->id);
                $user->password = Hash::make($request->password);
            }
    
            // Upload Gambar
            if ($request->hasFile('gambar')) {
                Log::info('File gambar ditemukan untuk user: ' . $user->id);
    
                // Hapus gambar lama jika ada
                if ($user->gambar) {
                    Storage::delete('public/profile_images/' . $user->gambar);
                    Log::info('Gambar lama dihapus: ' . $user->gambar);
                }
    
                // Simpan gambar baru
                $imagePath = $request->file('gambar')->store('profile_images', 'public');
                $user->gambar = basename($imagePath);
                Log::info('Gambar berhasil disimpan: ' . $user->gambar);
            }
    
            $user->save();
            Log::info('Profil berhasil diperbarui untuk user: ' . $user->id);
        } // Bagian dalam catch Exception
        catch (\Exception $e) {
            Log::error('Gagal menyimpan profil: ' . $e->getMessage());
            // Menambahkan return dengan error lebih eksplisit
            return back()->with('error', 'Gagal menyimpan profil, silakan coba lagi.');
                
        }
        
    
        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui');
    }
    
}
