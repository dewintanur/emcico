<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RuanganControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_ruangan()
    {
        // Given
        Ruangan::create([
            'nama_ruangan' => 'Stage Outdoor',
            'lantai' => 1,
            'tanggal' => '2025-04-06',
            'waktu_mulai' => '10:00:00',
            'waktu_selesai' => '12:00:00',
            'status' => 'Kosong',
        ]);

        // When
        $response = $this->get(route('ruangan.index'));

        // Then
        $response->assertStatus(200);
        $response->assertSee('Stage Outdoor');
    }

    /** @test */
    public function it_can_filter_ruangan_by_lantai()
    {
        Ruangan::create([
            'nama_ruangan' => 'Teras Tengah',
            'lantai' => 2,
            'tanggal' => '2025-04-06',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'status' => 'Kosong',
        ]);

        Ruangan::create([
            'nama_ruangan' => 'Lab Komputer',
            'lantai' => 4,
            'tanggal' => '2025-04-06',
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:00:00',
            'status' => 'Kosong',
        ]);

        $response = $this->get(route('ruangan.index', ['lantai' => 2]));

        $response->assertStatus(200);
        $response->assertSee('Teras Tengah');
        $response->assertDontSee('Lab Komputer');
    }

    
}
