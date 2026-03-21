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
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_prop', 10);
            $table->string('kode_kab', 10);
            $table->string('kode_kec', 10);
            $table->string('kode_desa', 10);
            $table->string('kode_blok', 15)->nullable();
            $table->string('nama_prop', 100);
            $table->string('nama_kab', 100);
            $table->string('nama_kec', 100);
            $table->string('nama_desa', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
