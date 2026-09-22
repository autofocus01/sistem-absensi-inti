<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_anomalies')) {
            return;
        }

        Schema::create('attendance_anomalies', function (Blueprint $table) {
            $table->id();
            $table->string('fingerprint', 64)->unique();
            $table->string('type', 80)->index();
            $table->string('severity', 20)->default('warning')->index();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('attendance_log_id')->nullable()->constrained('attendance_logs')->nullOnDelete();
            $table->string('overtime_submission_id')->nullable();
            $table->date('tanggal')->nullable()->index();
            $table->json('details')->nullable();
            $table->enum('status', ['OPEN', 'REVIEWED', 'RESOLVED', 'IGNORED'])->default('OPEN')->index();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'tanggal']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_anomalies');
    }
};
