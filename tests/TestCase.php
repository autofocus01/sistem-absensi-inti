<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * PENGAMAN KRITIS — dijalankan SETELAH aplikasi boot tetapi SEBELUM
     * setUpTraits() (RefreshDatabase → migrate:fresh).
     *
     * Riwayat: config cache (bootstrap/cache/config.php) membuat env
     * phpunit.xml (sqlite in-memory, APP_ENV=testing) diabaikan, sehingga
     * suite test pernah berjalan terhadap MySQL asli `absensi_inti` dan
     * migrate:fresh menghapus seluruh data. Gagal cepat jauh lebih baik
     * daripada kehilangan data.
     */
    public function createApplication()
    {
        if (is_file(dirname(__DIR__) . '/bootstrap/cache/config.php')) {
            throw new \RuntimeException(
                "[STOP] Config cache aktif (bootstrap/cache/config.php). " .
                'Env phpunit.xml tidak terbaca sehingga test akan menabrak database asli. ' .
                'Jalankan "php artisan optimize:clear" terlebih dahulu, lalu ulangi test.'
            );
        }

        $app = parent::createApplication();

        $koneksi = (string) config('database.default');
        if ($koneksi !== 'sqlite') {
            throw new \RuntimeException(
                "[STOP] Database test adalah \"{$koneksi}\", seharusnya sqlite :memory:. " .
                'Env phpunit.xml kemungkinan diabaikan. Test dihentikan sebelum '
                . 'menyentuh data asli.'
            );
        }

        return $app;
    }
}
