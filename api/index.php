<?php

// Ensure /tmp directories exist for Vercel's read-only filesystem
$tmpDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/framework/views',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Forward Vercel requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
