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
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('nama_kegiatan');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->dropColumn('tahun');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->year('tahun')->after('nama_kegiatan');
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
