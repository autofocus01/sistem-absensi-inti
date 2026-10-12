<?php

it('registers production preflight command', function () {
    config()->set('app.env', 'local');
    config()->set('app.debug', true);
    config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    config()->set('demo.enabled', true);

    dump([
        'app.env' => config('app.env'),
        'app.debug' => config('app.debug'),
        'app.key_exists' => filled(config('app.key')),
    ]);

    $this->artisan('production:preflight', [
        '--no-db' => true,
    ])->assertSuccessful();
});

/*
|--------------------------------------------------------------------------
| REGRESSION: demo clock harus gagalkan preflight pada production/staging.
| DEMO_MODE=true di production membuka manipulasi waktu presensi lewat UI /demo.
|--------------------------------------------------------------------------
*/
it('fails preflight when demo mode is enabled outside local/testing', function () {
    config()->set('app.env', 'production');
    config()->set('app.debug', false);
    config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    config()->set('demo.enabled', true);

    $this->artisan('production:preflight', ['--no-db' => true])
        ->expectsOutputToContain('FAIL DEMO_MODE_DISABLED')
        ->assertFailed();
});

it('passes preflight demo check when demo mode is disabled', function () {
    config()->set('app.env', 'production');
    config()->set('app.debug', false);
    config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    config()->set('demo.enabled', false);

    $this->artisan('production:preflight', ['--no-db' => true])
        ->expectsOutputToContain('PASS DEMO_MODE_DISABLED')
        ->assertSuccessful();
});