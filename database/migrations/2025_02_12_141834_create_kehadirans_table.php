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
            $table->unsignedBigInteger('ruangan_id')->nullable(); // FK ke ruangan
            $table->integer('lantai');
            $table->text('ttd'); // Tanda tangan
            $table->string('duty_officer');
            $table->enum('status', ['Booked', 'Checked-in', 'Checked-out']);
            $table->enum('status_konfirmasi', ['belum_dikonfirmasi', 'siap_checkout'])->default('belum_dikonfirmasi');
            $table->unsignedBigInteger('fo_id')->nullable(); // FK ke users
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('ruangan_id')->references('id')->on('ruangan')->onDelete('set null');
            $table->foreign('fo_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};
