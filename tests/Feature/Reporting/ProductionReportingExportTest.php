<?php

use App\Models\User;

it('allows hr to request excel and pdf reports', function () {
    $user = User::factory()->create(['role' => 'hr_admin']);
    $this->actingAs($user)->get(route('reports.excel'))->assertOk();
    $this->actingAs($user)->get(route('reports.pdf'))->assertOk();
});
