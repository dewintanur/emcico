<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class KelolaProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_profile_information()
    {
        // Arrange: Login user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Akses halaman profile
        $response = $this->get(route('profile'));

        // Assert: Pastikan halaman profile menampilkan data user
        $response->assertStatus(200);
        $response->assertSee($user->nama);
        $response->assertSee($user->email);
    }

    /** @test */
    public function it_allows_user_to_edit_profile()
    {
        // Arrange: Login user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Akses halaman edit profile
        $response = $this->get(route('profile.edit'));

        // Assert: Pastikan form edit profile ditampilkan
        $response->assertStatus(200);
        $response->assertSee('Edit Profil');
    }

    /** @test */
    public function it_allows_user_to_update_profile()
    {
        // Arrange: Login user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Data yang akan diupdate
        $data = [
            'nama' => 'Nama Baru',
            'email' => 'newemail@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        // Act: Kirim request untuk update profil
        $response = $this->post(route('profile.update'), $data);

        // Assert: Pastikan profil berhasil diperbarui
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success', 'Profil berhasil diperbarui');

        // Verifikasi bahwa data user telah diperbarui
        $this->assertEquals('Nama Baru', $user->fresh()->nama);
        $this->assertEquals('newemail@example.com', $user->fresh()->email);
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

  /** @test */
public function it_shows_error_if_profile_update_fails_due_to_validation()
{
    // Arrange: Login user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Data yang tidak valid (email kosong)
    $data = [
        'nama' => 'Nama Baru',
        'email' => '', // email kosong, yang menyebabkan error
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ];

    // Act: Kirim request untuk update profil
    $response = $this->post(route('profile.update'), $data);

    // Assert: Pastikan sistem menampilkan pesan error
    $response->assertSessionHasErrors('email');
}


}
