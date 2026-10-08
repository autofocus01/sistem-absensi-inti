<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows hr admin to open attendance recap', function () {
    $user = User::factory()->create(['role' => 'hr_admin']);

    $this->actingAs($user)
        ->get(route('attendance.index'))
        ->assertOk();
});

it('denies regular employee from attendance recap admin page', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $this->actingAs($user)
        ->get(route('attendance.index'))
        ->assertForbidden();
});

it('denies vp from attendance recap admin page', function () {
    $user = User::factory()->create(['role' => 'vp']);

    $this->actingAs($user)
        ->get(route('attendance.index'))
        ->assertForbidden();
});
