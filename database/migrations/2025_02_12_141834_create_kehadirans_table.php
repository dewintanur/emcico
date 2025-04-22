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
            $table->date('tanggal_ci')->nullable()->default(now()); // Make nullable or default to now()
            $table->unsignedBigInteger('ruangan_id')->nullable(); // FK ke ruangan
            $table->string('lantai')->nullable(); // Make lantai nullable
            $table->string('ttd')->nullable(); // Bisa null jika tidak wajib
            $table->string('duty_officer')->nullable(); // Bisa null jika tidak wajib
            $table->enum('status', ['Booked', 'Checked-in', 'Checked-out']);
            $table->enum('status_konfirmasi', ['belum_konfirmasi', 'belum_siap_checkout', 'siap_checkout']);
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
