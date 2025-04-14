<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusPengembalianToPeminjamanTable extends Migration
{
    public function up()
    {
        Schema::table('peminjaman_barang', function (Blueprint $table) {
            $table->enum('status_pengembalian', ['Belum Dikembalikan', 'Sudah Dikembalikan'])
                ->default('Belum Dikembalikan')
                ->after('deleted_at'); // sesuaikan posisi kolom
        });
    }

    public function down()
    {
        Schema::table('peminjaman_barang', function (Blueprint $table) {
            $table->dropColumn('status_pengembalian');
        });
    }
}
