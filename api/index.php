<?php

// Arahkan folder storage & bootstrap cache ke /tmp agar writable di Vercel
$app = require __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp');

require __DIR__ . '/../public/index.php';