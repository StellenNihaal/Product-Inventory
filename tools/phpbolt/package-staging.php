<?php

/**
 * Build a staging-ready folder with encrypted PHP and no plaintext source.
 *
 * Usage:
 *   php tools/phpbolt/package-staging.php
 *
 * Output:
 *   staging-build/
 */

$base = dirname(__DIR__, 2);
$out = $base . DIRECTORY_SEPARATOR . 'staging-build';
$backupDir = $base . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'phpbolt' . DIRECTORY_SEPARATOR . 'src-original';

if (!extension_loaded('bolt') || !function_exists('bolt_encrypt')) {
    fwrite(STDERR, "phpBolt is required locally to build the staging package.\n");
    exit(1);
}

if (!is_dir($backupDir . DIRECTORY_SEPARATOR . 'app')) {
    fwrite(STDERR, "Missing plaintext backup at tools/phpbolt/src-original\n");
    fwrite(STDERR, "If your live app/ is already plaintext, run encrypt once first.\n");
    exit(1);
}

// Refresh encrypted/ from plaintext backup without touching the live working tree.
passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($base . '/tools/phpbolt/encrypt.php') . ' --dist', $code);
if ($code !== 0) {
    exit($code);
}

if (is_dir($out)) {
    removeDirectory($out);
}
mkdir($out, 0777, true);

$includeDirs = [
    'bootstrap',
    'config',
    'public',
    'resources',
    'storage',
    'tests',
];

$includeFiles = [
    'artisan',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'vite.config.js',
    'phpunit.xml',
    '.env.example',
];

foreach ($includeDirs as $dir) {
    $from = $base . DIRECTORY_SEPARATOR . $dir;
    if (is_dir($from)) {
        copyDirectory($from, $out . DIRECTORY_SEPARATOR . $dir);
        echo "Copied {$dir}/\n";
    }
}

foreach ($includeFiles as $file) {
    $from = $base . DIRECTORY_SEPARATOR . $file;
    if (is_file($from)) {
        copy($from, $out . DIRECTORY_SEPARATOR . $file);
        echo "Copied {$file}\n";
    }
}

foreach (['app', 'routes'] as $dir) {
    copyDirectory(
        $base . DIRECTORY_SEPARATOR . 'encrypted' . DIRECTORY_SEPARATOR . $dir,
        $out . DIRECTORY_SEPARATOR . $dir
    );
    echo "Copied encrypted {$dir}/\n";
}

// Migrations must stay plaintext (Laravel needs return new class ...)
$databaseFrom = is_dir($backupDir . DIRECTORY_SEPARATOR . 'database')
    ? $backupDir . DIRECTORY_SEPARATOR . 'database'
    : $base . DIRECTORY_SEPARATOR . 'database';
copyDirectory($databaseFrom, $out . DIRECTORY_SEPARATOR . 'database');
echo "Copied plaintext database/\n";

// Keep storage structure, drop logs/cache/sessions contents.
cleanStorageForDeploy($out . DIRECTORY_SEPARATOR . 'storage');

// Never ship secrets or plaintext.
@unlink($out . DIRECTORY_SEPARATOR . '.env');
removeDirectory($out . DIRECTORY_SEPARATOR . 'tools');
removeDirectory($out . DIRECTORY_SEPARATOR . 'node_modules');
removeDirectory($out . DIRECTORY_SEPARATOR . 'vendor');
removeDirectory($out . DIRECTORY_SEPARATOR . '.git');

file_put_contents(
    $out . DIRECTORY_SEPARATOR . 'STAGING_DEPLOY.txt',
    <<<TXT
Staging deploy checklist
========================

1) Install phpBolt on the staging server
   - Download: https://phpbolt.com/download-phpbolt/
   - Match OS + PHP version (example Linux PHP 8.2: bolt.so)
   - Add to php.ini: extension=/full/path/to/bolt.so
   - Restart PHP-FPM / Apache / Nginx
   - Confirm: php -m | grep bolt

2) Upload this staging-build folder to the server
   - Document root must point to /public

3) On the server:
   cp .env.example .env
   # edit .env for staging DB, APP_URL, APP_ENV=staging, APP_DEBUG=false
   php artisan key:generate
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

4) Set permissions
   chmod -R 775 storage bootstrap/cache

5) Do NOT upload tools/phpbolt/src-original (plaintext). This package already excludes it.

TXT
);

echo "\nStaging package ready: staging-build/\n";
echo "Upload that folder to staging. See staging-build/STAGING_DEPLOY.txt\n";

function copyDirectory(string $from, string $to): void
{
    if (!is_dir($to)) {
        mkdir($to, 0777, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        $relative = substr($file->getPathname(), strlen($from) + 1);
        if ($relative === false) {
            continue;
        }
        $relativeUnix = str_replace('\\', '/', $relative);
        // Skip noisy / local-only paths and the public/storage link.
        if (preg_match('#^(logs|framework/(cache|sessions|views)|pail|storage)(/|$)#', $relativeUnix)) {
            continue;
        }
        if (is_link($file->getPathname())) {
            continue;
        }
        $target = $to . DIRECTORY_SEPARATOR . $relative;
        if ($file->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0777, true);
            }
            continue;
        }
        if (!$file->isFile()) {
            continue;
        }
        $dir = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        copy($file->getPathname(), $target);
    }
}

function removeDirectory(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($dir);
}

function cleanStorageForDeploy(string $storage): void
{
    foreach (['logs', 'framework/cache', 'framework/sessions', 'framework/views'] as $rel) {
        $path = $storage . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->getFilename() === '.gitignore') {
                continue;
            }
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }
    }
}
