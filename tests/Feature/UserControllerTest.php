<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admin user dummy untuk login.
     */
    protected function adminUser()
    {
        return User::factory()->create([
            'role' => 'it', // atau 'admin' kalau kamu punya role admin
        ]);
    }

    /** @test */
    public function admin_can_add_a_new_user()
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'nama' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'role' => 'marketing',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'email' => 'baru@example.com',
            'nama' => 'Pengguna Baru',
            'role' => 'marketing',
        ]);
    }

    /** @test */
    public function admin_can_update_a_user()
    {
        $admin = $this->adminUser();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->put(route('users.update', $user->id), [
            'nama' => 'Nama Baru',
            'email' => 'namabaru@example.com',
            'role' => 'produksi',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nama' => 'Nama Baru',
            'email' => 'namabaru@example.com',
            'role' => 'produksi',
        ]);
    }

    /** @test */
    public function admin_can_delete_a_user()
    {
        $admin = $this->adminUser();
        $user = User::factory()->create();

        // Hapus user
        $response = $this->actingAs($admin)->delete(route('users.destroy', $user->id));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
