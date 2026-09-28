<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('overtime_submissions')) {
            return;
        }

        Schema::table('overtime_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('overtime_submissions', 'rounded_minutes')) {
                $table->unsignedInteger('rounded_minutes')->default(0)->after('actual_minutes');
            }

            if (! Schema::hasColumn('overtime_submissions', 'excess_minutes')) {
                $table->unsignedInteger('excess_minutes')->default(0)->after('recognized_minutes');
            }

            if (! Schema::hasColumn('overtime_submissions', 'anomaly_codes')) {
                $table->json('anomaly_codes')->nullable()->after('eligibility_status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('overtime_submissions')) {
            return;
        }

        Schema::table('overtime_submissions', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('overtime_submissions', 'rounded_minutes')) {
                $columns[] = 'rounded_minutes';
            }
            if (Schema::hasColumn('overtime_submissions', 'excess_minutes')) {
                $columns[] = 'excess_minutes';
            }
            if (Schema::hasColumn('overtime_submissions', 'anomaly_codes')) {
                $columns[] = 'anomaly_codes';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
