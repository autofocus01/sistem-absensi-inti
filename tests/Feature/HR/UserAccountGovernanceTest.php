<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('allows HR to create a karyawan account from an employee master', function () {
    $hr = User::factory()->create([
        'role' => 'hr_admin',
        'is_active' => true,
    ]);

    $division = Division::create([
        'nama' => 'UAT Division',
        'lini' => 'SEVP',
    ]);

    $employee = Employee::create([
        'nipeg' => '91000001',
        'nama' => 'Karyawan UAT',
        'jabatan' => 'Staff',
        'band_code' => '2',
        'division_id' => $division->id,
    ]);

    $response = $this->actingAs($hr)->post(route('employees.account.store', $employee), [
        'email' => '91000001@example.test',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect(route('employee-accounts.index'));

    $user = User::where('email', '91000001@example.test')->firstOrFail();

    expect($user->role)->toBe('karyawan')
        ->and($user->is_active)->toBeTrue()
        ->and($user->must_change_password)->toBeTrue()
        ->and(Hash::check('Password123!', $user->password))->toBeTrue();

    expect($employee->fresh()->user_id)->toBe($user->id);
});

it('does not allow creating a second account for the same employee', function () {
    $hr = User::factory()->create([
        'role' => 'hr_admin',
        'is_active' => true,
    ]);

    $division = Division::create([
        'nama' => 'UAT Division',
        'lini' => 'SEVP',
    ]);

    $employee = Employee::create([
        'nipeg' => '91000002',
        'nama' => 'Karyawan Existing',
        'division_id' => $division->id,
    ]);

    $existing = User::factory()->create([
        'role' => 'karyawan',
        'is_active' => true,
    ]);
    $employee->user()->associate($existing);
    $employee->save();

    $response = $this->actingAs($hr)->post(route('employees.account.store', $employee), [
        'email' => 'another@example.test',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertStatus(409);
    expect(User::where('email', 'another@example.test')->exists())->toBeFalse();
});

it('blocks login for an inactive account', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
        'is_active' => false,
        'password' => Hash::make('password'),
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});


it('prevents HR from deleting an employee while a login account is attached', function () {
    $hr = User::factory()->create([
        'role' => 'hr_admin',
        'is_active' => true,
    ]);

    $division = Division::create([
        'nama' => 'UAT Division',
        'lini' => 'SEVP',
    ]);

    $employee = Employee::create([
        'nipeg' => '91000003',
        'nama' => 'Karyawan Terhubung',
        'division_id' => $division->id,
    ]);

    $user = User::factory()->create([
        'role' => 'karyawan',
        'is_active' => true,
    ]);
    $employee->user()->associate($user);
    $employee->save();

    $response = $this->actingAs($hr)->delete(route('employees.destroy', $employee));

    $response->assertRedirect(route('employees.index'));
    expect(Employee::find($employee->id))->not->toBeNull();
});

it('does not allow a linked employee to delete their own account', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
        'is_active' => true,
    ]);

    $division = Division::create([
        'nama' => 'UAT Division',
        'lini' => 'SEVP',
    ]);

    $employee = Employee::create([
        'nipeg' => '91000004',
        'nama' => 'Karyawan Linked',
        'division_id' => $division->id,
    ]);
    $employee->user()->associate($user);
    $employee->save();

    $response = $this->actingAs($user)->delete(route('profile.destroy'), [
        'password' => 'password',
    ]);

    $response->assertRedirect(route('profile.edit'));
    expect($user->fresh())->not->toBeNull();
});
