<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        // Membuat user dengan role 'it' atau 'admin'
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'), // Ganti dengan password yang valid
            'role' => 'it' // atau 'admin' jika ingin menguji role admin
        ]);

        // Melakukan post request login
        $response = $this->post(route('login'), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        // Pastikan login berhasil dan diarahkan ke route yang sesuai dengan role
        $response->assertRedirect(route('users.index')); // Mengarahkan ke 'it' atau 'admin' route
        $response->assertSessionHas('success');
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        // Mengirimkan kredensial yang salah
        $response = $this->post(route('login'), [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        // Memastikan bahwa login gagal dan tetap di halaman login
        $response->assertRedirect(route('login.form'));
        $response->assertSessionHas('error');
    }
}
