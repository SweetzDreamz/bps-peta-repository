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
        Schema::table('peta', function (Blueprint $table) {
            $table->dropColumn('versi');
        });
    }

    public function down(): void
    {
        Schema::table('peta', function (Blueprint $table) {
            $table->string('versi', 20)->default('1.0')->after('tahun');
        });
    }

};
