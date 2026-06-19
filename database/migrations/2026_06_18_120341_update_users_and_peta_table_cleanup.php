<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus kolom users hanya jika masih ada
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });

        // Hapus foreign key dan kolom sls_id di tabel peta
        Schema::table('peta', function (Blueprint $table) {
            if (Schema::hasColumn('peta', 'sls_id')) {
                $table->dropForeign('peta_sls_id_foreign'); 
                $table->dropColumn('sls_id');
            }
        });

        // 1. Perluas dulu ENUM agar bisa menampung 'WS' sementara waktu
        DB::statement("ALTER TABLE peta MODIFY COLUMN jenis_peta ENUM('WA','WB','SLS','WS') NOT NULL");

        // 2. Sekarang aman untuk mengubah data SLS menjadi WS
        DB::statement("UPDATE peta SET jenis_peta = 'WS' WHERE jenis_peta = 'SLS'");

        // 3. (Opsional) Jika ada data 'WB' yang ingin diubah ke 'WS' juga, lakukan di sini:
        // DB::statement("UPDATE peta SET jenis_peta = 'WS' WHERE jenis_peta = 'WB'");

        // 4. Terakhir, pangkas ENUM menjadi hanya opsi final yang diinginkan
        DB::statement("ALTER TABLE peta MODIFY COLUMN jenis_peta ENUM('WA','WS') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
        });

        Schema::table('peta', function (Blueprint $table) {
            if (!Schema::hasColumn('peta', 'sls_id')) {
                $table->unsignedBigInteger('sls_id')->nullable()->after('wilayah_id');
            }
        });

        DB::statement("ALTER TABLE peta MODIFY COLUMN jenis_peta ENUM('WA','WB','SLS') NOT NULL");
        DB::statement("UPDATE peta SET jenis_peta = 'SLS' WHERE jenis_peta = 'WS'");
    }
};