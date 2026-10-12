<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;

function makeForcedPasswordUser(): User
{
    $user = User::factory()->create([
        'role' => 'karyawan',
        'must_change_password' => true,
        'password' => 'TempPass123!',
    ]);

    $division = Division::create(['nama' => 'Div ' . uniqid(), 'lini' => 'UAT']);
    Employee::create([
        'user_id' => $user->id,
        'nipeg' => 'FC' . uniqid(),
        'nama' => 'Force Change',
        'division_id' => $division->id,
        'band_code' => 2,
    ]);

    return $user;
}

it('blocks app access until temporary password is changed', function () {
    $user = makeForcedPasswordUser();

    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('profile.edit'));

    $this->actingAs($user)->get('/attendance/my')
        ->assertRedirect(route('profile.edit'));
});

it('allows access to profile page and logout while password change is pending', function () {
    $user = makeForcedPasswordUser();

    $this->actingAs($user)->get('/profile')->assertOk();
});

it('rejects non-GET requests while password change is pending', function () {
    $user = makeForcedPasswordUser();

    $this->actingAs($user)->post('/attendance/my/overtime-dismiss')->assertStatus(423);
});

it('restores full access after password change', function () {
    $user = makeForcedPasswordUser();

    $user->forceFill(['must_change_password' => false])->save();

    $this->actingAs($user)->get('/dashboard')->assertOk();
});
