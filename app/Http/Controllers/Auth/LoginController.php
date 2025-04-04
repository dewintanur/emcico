<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class LoginController extends Controller
{
   // Menampilkan halaman login
   public function showLoginForm()
   {
       return view('login'); // Pastikan file ini ada di resources/views/auth/login.blade.php
   }

   // Proses login
   public function login(Request $request)
   {
       $request->validate([
           'email' => 'required|email',
           'password' => 'required'
       ]);

       // Coba autentikasi
       if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
           $user = Auth::user();
           
           // Update last_login
           $user->update(['last_login' => Carbon::now()]);

           // Simpan log aktivitas "Login"
           LogAktivitas::create([
               'user_id' => $user->id,
               'aktivitas' => 'Login',
               'waktu' => Carbon::now(),
           ]);

           // Catat di log sistem
           Log::info('User logged in: ' . $user->name . ' (' . $user->email . ') - Role: ' . $user->role);

           // Simpan role dalam session
           session(['active_role' => $user->role]);

           // Redirect berdasarkan role
           switch ($user->role) {
               case 'front_office':
                   return redirect()->route('fo.bookingList')->with('success', 'Login berhasil');
               case 'marketing':
                   return redirect()->route('marketing.peminjaman')->with('success', 'Login berhasil');
               case 'it':
                   return redirect()->route('users.index')->with('success', 'Login berhasil');
               case 'produksi':
                   return redirect()->route('produksi.peminjaman')->with('success', 'Login berhasil');
               case 'duty_officer':
                   return redirect()->route('ruangan.index')->with('success', 'Login berhasil');
               case 'admin':
                   return redirect()->route('admin.dashboard')->with('success', 'Login berhasil');
               default:
                   return redirect('/')->with('success', 'Login berhasil');
           }
       }

       return back()->with('error', 'Email atau password salah');
   }

   // Proses logout
   public function logout()
   {
       $user = Auth::user();

       if ($user) {
           // Simpan log aktivitas "Logout"
           LogAktivitas::create([
               'user_id' => $user->id,
               'aktivitas' => 'Logout',
               'waktu' => Carbon::now(),
           ]);

           Log::info('User logged out: ' . $user->name . ' (' . $user->email . ')');
       }

       Auth::logout();
       return redirect()->route('login.form')->with('success', 'Berhasil logout');
   }
}
