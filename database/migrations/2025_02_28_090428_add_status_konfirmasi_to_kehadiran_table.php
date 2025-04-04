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
            Schema::table('kehadiran', function (Blueprint $table) {
                $table->enum('status_konfirmasi', ['belum_dikonfirmasi', 'siap_checkout'])
                      ->default('belum_dikonfirmasi')
                      ->after('status'); // Sesuaikan dengan kolom mana yang ingin kamu letakkan setelahnya
            });
        }
    
        public function down()
        {
            Schema::table('kehadiran', function (Blueprint $table) {
                $table->dropColumn('status_konfirmasi');
            });
        }
    };
