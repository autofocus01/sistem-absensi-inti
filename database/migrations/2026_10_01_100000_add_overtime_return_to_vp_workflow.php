<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_submissions', function (Blueprint $table) {
            $table->foreignId('hr_returned_by_id')->nullable()->after('hr_verifier_id')->constrained('users')->nullOnDelete();
            $table->timestamp('hr_returned_at')->nullable()->after('hr_verified_at');
            $table->text('hr_return_reason')->nullable()->after('hr_notes');
            $table->timestamp('vp_reverified_at')->nullable()->after('vp_approved_at');
            $table->text('vp_reverification_notes')->nullable()->after('vp_notes');
            $table->index(['status', 'division_id']);
        });

        // The original workflow used a MySQL ENUM. A return-to-VP state is
        // a legitimate extension of the state machine, so use a bounded
        // string instead of repeatedly altering an ENUM in future releases.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE overtime_submissions MODIFY status VARCHAR(32) NOT NULL DEFAULT 'PENDING_VP'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE overtime_submissions ALTER COLUMN status TYPE VARCHAR(32) USING status::VARCHAR");
            DB::statement("ALTER TABLE overtime_submissions ALTER COLUMN status SET DEFAULT 'PENDING_VP'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Preserve rollback safety by normalising the new state before
            // restoring the original ENUM constraint.
            DB::table('overtime_submissions')
                ->where('status', 'RETURNED_TO_VP')
                ->update(['status' => 'APPROVED_VP']);

            DB::statement("ALTER TABLE overtime_submissions MODIFY status ENUM('PENDING_VP','POST_SUBMITTED','APPROVED_VP','REJECTED_VP','VERIFIED_HR','REJECTED_HR') NOT NULL DEFAULT 'PENDING_VP'");
        } elseif ($driver === 'pgsql') {
            DB::table('overtime_submissions')
                ->where('status', 'RETURNED_TO_VP')
                ->update(['status' => 'APPROVED_VP']);
        }

        Schema::table('overtime_submissions', function (Blueprint $table) {
            $table->dropForeign(['hr_returned_by_id']);
            $table->dropColumn([
                'hr_returned_by_id',
                'hr_returned_at',
                'hr_return_reason',
                'vp_reverified_at',
                'vp_reverification_notes',
            ]);
            $table->dropIndex(['status', 'division_id']);
        });
    }
};
