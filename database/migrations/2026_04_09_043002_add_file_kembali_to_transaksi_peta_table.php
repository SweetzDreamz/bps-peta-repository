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
        Schema::table('transaksi_peta', function (Blueprint $table) {
            $table->string('file_kembali', 500)->nullable()->after('status');
            $table->timestamp('waktu_kembali')->nullable()->after('file_kembali');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_peta', function (Blueprint $table) {
            $table->dropColumn(['file_kembali', 'waktu_kembali']);
        });
    }
};
