<?php

use App\Services\Attendance\AttendanceBusinessRules;
use Carbon\Carbon;

beforeEach(function () {
    $this->rules = new AttendanceBusinessRules();
    $this->workday = Carbon::parse('2026-09-21'); // Senin
});

test('07:30 tepat waktu dan tidak ada grace period', function () {
    expect($this->rules->latenessMinutes($this->workday, '07:30:00'))->toBe(0)
        ->and($this->rules->latenessMinutes($this->workday, '07:31:00'))->toBe(1)
        ->and($this->rules->latenessMinutes($this->workday, '08:00:00'))->toBe(30);
});

test('datang sebelum 07:30 tidak dihitung terlambat', function () {
    expect($this->rules->latenessMinutes($this->workday, '07:29:59'))->toBe(0);
});

test('sabtu minggu tidak menghasilkan keterlambatan jam kerja normal', function () {
    $saturday = Carbon::parse('2026-09-19');

    expect($this->rules->latenessMinutes($saturday, '08:00:00'))->toBe(0);
});

test('durasi kerja dihitung dari jam aktual tanpa mengubah data sumber', function () {
    expect($this->rules->workDurationMinutes($this->workday, '07:30:00', '16:30:00'))->toBe(540)
        ->and($this->rules->workDurationMinutes($this->workday, '07:35:00', '16:45:00'))->toBe(550);
});

test('clock out sebelum clock in ditolak', function () {
    expect(fn () => $this->rules->workDurationMinutes($this->workday, '08:00:00', '07:59:00'))
        ->toThrow(InvalidArgumentException::class);
});

test('16:30 tepat bukan waktu setelah jam kerja normal', function () {
    expect($this->rules->isAfterNormalEnd($this->workday, '16:30:00'))->toBeFalse();
});

test('16:31 sampai 16:59 berada setelah jam normal tetapi belum menentukan lembur yang diakui', function () {
    expect($this->rules->isAfterNormalEnd($this->workday, '16:31:00'))->toBeTrue()
        ->and($this->rules->isAfterNormalEnd($this->workday, '16:59:00'))->toBeTrue();
});

test('17:00 dan seterusnya berada setelah jam normal', function () {
    expect($this->rules->isAfterNormalEnd($this->workday, '17:00:00'))->toBeTrue()
        ->and($this->rules->isAfterNormalEnd($this->workday, '17:30:00'))->toBeTrue();
});
