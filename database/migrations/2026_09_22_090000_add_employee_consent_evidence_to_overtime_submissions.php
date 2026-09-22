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
            $table->boolean('employee_consent')
                ->default(false)
                ->after('reason');

            $table->timestamp('employee_consent_at')
                ->nullable()
                ->after('employee_consent');

            $table->ipAddress('employee_consent_ip')
                ->nullable()
                ->after('employee_consent_at');

            $table->text('employee_consent_user_agent')
                ->nullable()
                ->after('employee_consent_ip');

            $table->string('employee_consent_source', 32)
                ->nullable()
                ->after('employee_consent_user_agent');

            $table->string('employee_consent_version', 32)
                ->nullable()
                ->after('employee_consent_source');

            $table->text('employee_consent_text')
                ->nullable()
                ->after('employee_consent_version');

            $table->index(['employee_consent', 'employee_consent_at']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('overtime_submissions')) {
            return;
        }

        Schema::table('overtime_submissions', function (Blueprint $table) {
            $table->dropIndex(['employee_consent', 'employee_consent_at']);
            $table->dropColumn([
                'employee_consent',
                'employee_consent_at',
                'employee_consent_ip',
                'employee_consent_user_agent',
                'employee_consent_source',
                'employee_consent_version',
                'employee_consent_text',
            ]);
        });
    }
};
