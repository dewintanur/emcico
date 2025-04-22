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
        Schema::create('list_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->integer('jumlah');
            $table->string('satuan'); // Tambahkan kolom 'satuan' setelah kolom 'jumlah'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_barang');
    }
};
