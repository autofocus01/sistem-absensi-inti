<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees')) {
            return;
        }

        if (! Schema::hasColumn('employees', 'band_code')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedTinyInteger('band_code')
                    ->nullable()
                    ->after('jabatan')
                    ->index();
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('employees')
            && Schema::hasColumn('employees', 'band_code')
        ) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropIndex(['band_code']);
                $table->dropColumn('band_code');
            });
        }
    }
};