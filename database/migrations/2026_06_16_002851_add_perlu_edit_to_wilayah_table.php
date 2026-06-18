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
        Schema::table('wilayah', function (Blueprint $table) {
            $table->boolean('perlu_edit')->default(false)->after('induk_id');
        });
    }

    public function down(): void
    {
        Schema::table('wilayah', function (Blueprint $table) {
            $table->dropColumn('perlu_edit');
        });
    }
};