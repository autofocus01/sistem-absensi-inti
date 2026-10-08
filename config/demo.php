<?php

return [
    'enabled' => (bool) env('DEMO_MODE', false),
    'timezone' => env('DEMO_TIMEZONE', 'Asia/Jakarta'),
    'presets' => [
        '07:30' => '07:30 — Clock In',
        '16:30' => '16:30 — Jam Kerja Selesai',
        '18:00' => '18:00 — Simulasi Lembur',
    ],
];
