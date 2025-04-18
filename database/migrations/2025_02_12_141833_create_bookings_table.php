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
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking')->unique();
            $table->date('tanggal');
            $table->string('nama_event');
            $table->string('nama_organisasi');
            $table->string('kategori_event');
            $table->string('kategori_ekraf');
            $table->string('jenis_event');
            $table->unsignedBigInteger('ruangan_id');
            $table->integer('lantai');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('nama_pic');
            $table->string('no_pic');
            $table->enum('status', ['Booking', 'Approved', 'Rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
