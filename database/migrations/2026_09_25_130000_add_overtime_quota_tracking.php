<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('overtime_submissions')) return;

        Schema::table('overtime_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('overtime_submissions', 'quota_reserved_minutes')) {
                $table->unsignedInteger('quota_reserved_minutes')->default(0)->after('recognized_minutes');
            }
            if (! Schema::hasColumn('overtime_submissions', 'quota_used_minutes')) {
                $table->unsignedInteger('quota_used_minutes')->default(0)->after('quota_reserved_minutes');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('overtime_submissions')) return;
        Schema::table('overtime_submissions', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('overtime_submissions', 'quota_used_minutes')) $columns[] = 'quota_used_minutes';
            if (Schema::hasColumn('overtime_submissions', 'quota_reserved_minutes')) $columns[] = 'quota_reserved_minutes';
            if ($columns !== []) $table->dropColumn($columns);
        });
    }
};
