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
            $table->softDeletes(); // akan menambahkan kolom deleted_at bertipe timestamp nullable
        });
    }
    
    public function down()
    {
        Schema::table('peminjaman_barang', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
    
};
