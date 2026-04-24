<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks dulu
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('wilayah');

        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('id_subsls')->nullable();
            $table->string('nama_sls', 100);
            $table->string('nama_ketua', 100)->nullable();
            $table->string('jenis', 50)->nullable();
            $table->integer('kode_prop')->nullable();
            $table->integer('kode_kab')->nullable();
            $table->integer('kode_kec')->nullable();
            $table->integer('kode_des')->nullable();
            $table->integer('kode_sls')->nullable();
            $table->integer('kode_subsls')->nullable();
            $table->integer('klas')->nullable();
            $table->string('nama_prop', 100)->nullable();
            $table->string('nama_kab', 100)->nullable();
            $table->string('nama_kec', 100)->nullable();
            $table->string('nama_des', 100)->nullable();
            $table->integer('kk')->default(0);
            $table->integer('btt')->default(0);
            $table->integer('bttk')->default(0);
            $table->integer('bku')->default(0);
            $table->integer('bbtt_nonusaha')->default(0);
            $table->integer('usaha')->default(0);
            $table->integer('muatan')->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->enum('asal', ['import', 'gabung', 'pecah'])->default('import');
            $table->json('wilayah_asal_ids')->nullable();
            $table->unsignedBigInteger('induk_id')->nullable();
            $table->foreign('induk_id')->references('id')->on('wilayah')->onDelete('set null');
            $table->timestamps();
        });

        // Enable kembali
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};