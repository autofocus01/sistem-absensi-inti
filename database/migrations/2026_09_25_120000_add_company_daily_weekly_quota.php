<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_overtime_rules')) return;

        Schema::table('company_overtime_rules', function (Blueprint $table) {
            if (! Schema::hasColumn('company_overtime_rules', 'maximum_minutes_daily')) {
                $table->unsignedInteger('maximum_minutes_daily')->nullable()->after('maximum_minutes_event');
            }
            if (! Schema::hasColumn('company_overtime_rules', 'maximum_minutes_weekly')) {
                $table->unsignedInteger('maximum_minutes_weekly')->nullable()->after('maximum_minutes_daily');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('company_overtime_rules')) return;

        Schema::table('company_overtime_rules', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('company_overtime_rules', 'maximum_minutes_weekly')) $columns[] = 'maximum_minutes_weekly';
            if (Schema::hasColumn('company_overtime_rules', 'maximum_minutes_daily')) $columns[] = 'maximum_minutes_daily';
            if ($columns !== []) $table->dropColumn($columns);
        });
    }
};
