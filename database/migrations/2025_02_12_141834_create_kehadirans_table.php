<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking');
            $table->string('nama_ci');
            $table->string('no_ci');
            $table->date('tanggal_ci');
            $table->string('ruangan');
            $table->integer('lantai');
            $table->text('ttd'); // Untuk menyimpan tanda tangan dalam bentuk file path atau base64
            $table->string('duty_officer');
            $table->enum('status', ['Booked', 'Checked-in', 'Checked-out']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
    }
};
