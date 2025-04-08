<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('peminjaman_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_id')->after('kode_booking');
        });
    }
    
    public function down()
    {
        Schema::table('peminjaman_barang', function (Blueprint $table) {
            $table->dropColumn('barang_id');
        });
    }
    
};
