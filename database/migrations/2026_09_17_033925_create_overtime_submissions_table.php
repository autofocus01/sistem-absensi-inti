<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('overtime_submissions')) {
        Schema::create('overtime_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('division_id')->constrained('divisions');
            $table->date('overtime_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('estimated_hours', 4, 2);
            $table->enum('category', ['hari_kerja', 'akhir_pekan', 'hari_libur_nasional'])->default('hari_kerja');
            $table->text('reason');
            
            // Status Workflow State Machine
            $table->enum('status', [
                'PENDING_VP',
                'POST_SUBMITTED',
                'APPROVED_VP',
                'REJECTED_VP',
                'VERIFIED_HR',
                'REJECTED_HR'
            ])->default('PENDING_VP');

            // Audit Trail VP Approval
            $table->foreignId('vp_approver_id')->nullable()->constrained('users');
            $table->timestamp('vp_approved_at')->nullable();
            $table->text('vp_notes')->nullable();

            // Audit Trail HR Verification
            $table->foreignId('hr_verifier_id')->nullable()->constrained('users');
            $table->timestamp('hr_verified_at')->nullable();
            $table->text('hr_notes')->nullable();

            $table->timestamps();
        });
        };
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_submissions');
    }
};