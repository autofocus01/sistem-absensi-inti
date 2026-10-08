<?php

use App\Models\AuditLog;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk();
});

test('profile identity is managed by HR or administrator', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
        'is_active' => true,
    ]);
    $originalName = $user->name;
    $originalEmail = $user->email;

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Nama Tidak Diizinkan',
            'email' => 'blocked@example.com',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile')
        ->assertSessionHas('status', 'identity-managed-by-hr');

    $user->refresh();

    expect($user->name)->toBe($originalName)
        ->and($user->email)->toBe($originalEmail)
        ->and(AuditLog::where('user_id', $user->id)->where('action', 'PROFILE_UPDATED')->exists())->toBeFalse();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user cannot delete their own account because lifecycle is administrator-managed', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile')
        ->assertSessionHas('status', 'Penghapusan akun dikelola administrator sistem dan tidak dapat dilakukan sendiri.');

    expect($user->fresh())->not->toBeNull();
});

test('profile displays the linked employee band code as read-only data', function () {
    $user = User::factory()->create(['role' => 'karyawan']);
    $division = \App\Models\Division::create(['nama' => 'Divisi Profil Band Test']);
    $employee = \App\Models\Employee::create([
        'nipeg' => '90019999',
        'nama' => 'Profil Band Test',
        'jabatan' => 'Karyawan',
        'band_code' => 3,
        'division_id' => $division->id,
    ]);
    $employee->user()->associate($user);
    $employee->save();

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Band Code')
        ->assertSee('Band 3');

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'band_code' => 4,
        ])
        ->assertSessionHasNoErrors();

    expect($employee->fresh()->band_code)->toBe(3);
});
