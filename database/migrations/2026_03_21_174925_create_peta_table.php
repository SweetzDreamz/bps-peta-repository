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
        Schema::create('peta', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_peta', ['WA', 'WB', 'SLS']);
            $table->foreignId('wilayah_id')->constrained('wilayah')->onDelete('cascade');
            $table->foreignId('sls_id')->nullable()->constrained('sls')->onDelete('set null');
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
            $table->year('tahun');
            $table->string('versi', 20)->default('1.0');
            $table->string('path_file', 500);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peta');
    }
};
