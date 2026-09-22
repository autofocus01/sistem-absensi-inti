<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('direktur utama dapat membuka dashboard eksekutif tanpa legacy AttendanceLog overtime method', function () {
    $user = User::factory()->create([
        'role' => 'direktur_utama',
    ]);

    $response = $this->actingAs($user)
        ->get(route('direktur.dashboard'));

    $response->assertOk();
});

test('non direktur tidak dapat membuka dashboard eksekutif', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $response = $this->actingAs($user)
        ->get(route('direktur.dashboard'));

    $response->assertForbidden();
});