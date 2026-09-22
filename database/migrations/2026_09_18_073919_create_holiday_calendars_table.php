<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holiday_calendars', function (Blueprint $table) {
            $table->id();

            $table->date('tanggal')->unique();

            $table->string('nama', 255);

            $table->string('jenis', 50)->default('NATIONAL_HOLIDAY');

            $table->boolean('is_active')->default(true);

            $table->string('source_document', 255)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(
                ['tanggal', 'is_active'],
                'holiday_calendar_date_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_calendars');
    }
};