<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_submissions', function (Blueprint $table) {
            $table->string('submission_mode', 24)->default('PRE_SUBMITTED')->after('reason');
            $table->foreignId('attendance_log_id')->nullable()->after('submission_mode')->constrained('attendance_logs')->nullOnDelete();
            $table->time('actual_start_time')->nullable()->after('end_time');
            $table->time('actual_end_time')->nullable()->after('actual_start_time');
            $table->unsignedInteger('actual_minutes')->nullable()->after('actual_end_time');
            $table->unsignedInteger('recognized_minutes')->nullable()->after('actual_minutes');
            $table->string('eligibility_status', 24)->nullable()->after('recognized_minutes');
            $table->index(['overtime_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('overtime_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attendance_log_id');
            $table->dropColumn([
                'submission_mode', 'actual_start_time', 'actual_end_time',
                'actual_minutes', 'recognized_minutes', 'eligibility_status',
            ]);
        });
    }
};
