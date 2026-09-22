<?php

it('registers production preflight command', function () {
    config()->set('app.env', 'local');
    config()->set('app.debug', true);
    config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

    dump([
        'app.env' => config('app.env'),
        'app.debug' => config('app.debug'),
        'app.key_exists' => filled(config('app.key')),
    ]);

    $this->artisan('production:preflight', [
        '--no-db' => true,
    ])->assertSuccessful();
});