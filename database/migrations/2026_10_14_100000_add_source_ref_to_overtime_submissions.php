<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('overtime_submissions', 'source_ref')) {
                /*
                 * Nomor dokumen sumber (NOSPKL) saat lembur berasal dari
                 * impor file SPKL. Menjadi jejak provenance per baris sekaligus
                 * kunci deduplikasi agar file yang sama tidak menggandakan
                 * pengajuan saat diimpor ulang.
                 */
                $table->string('source_ref')->nullable()->after('ticket_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('overtime_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('overtime_submissions', 'source_ref')) {
                $table->dropColumn('source_ref');
            }
        });
    }
};
