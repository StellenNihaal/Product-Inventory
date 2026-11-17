<?php

/**
 * phpBolt encoder for this Laravel app.
 *
 * Encrypts app/ and routes/ PHP files.
 * database/migrations are NOT encrypted — Laravel needs their
 * `return new class` value, which phpBolt's wrapper breaks.
 *
 * Usage:
 *   php tools/phpbolt/encrypt.php           Backup + encrypt + apply
 *   php tools/phpbolt/encrypt.php --apply   Same as default
 *   php tools/phpbolt/encrypt.php --dist    Only write to encrypted/
 *   php tools/phpbolt/encrypt.php --restore Restore plaintext from backup
 */

$base = dirname(__DIR__, 2);
$backupDir = $base . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'phpbolt' . DIRECTORY_SEPARATOR . 'src-original';
$distDir = $base . DIRECTORY_SEPARATOR . 'encrypted';
$sources = ['app', 'routes'];
$mode = 'apply';

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--restore') {
        $mode = 'restore';
    } elseif ($arg === '--dist') {
        $mode = 'dist';
    } elseif ($arg === '--apply') {
        $mode = 'apply';
    }
}

if (!extension_loaded('bolt') || !function_exists('bolt_encrypt')) {
    fwrite(STDERR, "phpBolt is not loaded. Expected extension=php_bolt.dll in php.ini\n");
    fwrite(STDERR, "PHP " . PHP_VERSION . "  ini=" . php_ini_loaded_file() . "\n");
    fwrite(STDERR, "ext=" . ini_get('extension_dir') . "\n");
    exit(1);
}

if ($mode === 'restore') {
    if (!is_dir($backupDir)) {
        fwrite(STDERR, "No plaintext backup found at {$backupDir}\n");
        exit(1);
    }
    foreach ($sources as $source) {
        $from = $backupDir . DIRECTORY_SEPARATOR . $source;
        $to = $base . DIRECTORY_SEPARATOR . $source;
        if (!is_dir($from)) {
            continue;
        }
        copyDirectory($from, $to);
        echo "Restored {$source}/\n";
    }
    echo "Plaintext source restored.\n";
    exit(0);
}

foreach ($sources as $source) {
    $live = $base . DIRECTORY_SEPARATOR . $source;
    if (!is_dir($live)) {
        fwrite(STDERR, "Missing source directory: {$live}\n");
        exit(1);
    }
}

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$sample = $base . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Product.php';
$liveIsPlaintext = is_file($sample) && !str_contains((string) file_get_contents($sample), 'bolt_decrypt(');

foreach ($sources as $source) {
    $live = $base . DIRECTORY_SEPARATOR . $source;
    $backup = $backupDir . DIRECTORY_SEPARATOR . $source;
    if ($liveIsPlaintext || !is_dir($backup) || countPlainPhpFiles($backup) === 0) {
        if (is_dir($backup)) {
            removeDirectory($backup);
        }
        copyDirectory($live, $backup);
        echo "Backed up {$source}/ to tools/phpbolt/src-original/{$source}/\n";
    }
}

if (is_dir($distDir)) {
    removeDirectory($distDir);
}
mkdir($distDir, 0777, true);

$encrypted = 0;
$copied = 0;

foreach ($sources as $source) {
    $fromRoot = $backupDir . DIRECTORY_SEPARATOR . $source;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($fromRoot, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        $relative = substr($file->getPathname(), strlen($fromRoot) + 1);
        $target = $distDir . DIRECTORY_SEPARATOR . $source . DIRECTORY_SEPARATOR . $relative;
        $targetDir = dirname($target);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if ($file->isDir()) {
            continue;
        }

        $name = $file->getFilename();
        $isPhp = str_ends_with(strtolower($name), '.php');
        $isBlade = str_ends_with(strtolower($name), '.blade.php');

        if (!$isPhp || $isBlade) {
            copy($file->getPathname(), $target);
            $copied++;
            continue;
        }

        encryptPhpFile($file->getPathname(), $target);
        $encrypted++;
        echo "Encrypted {$source}/{$relative}\n";
    }
}

echo "Encrypted {$encrypted} PHP files, copied {$copied} other files to encrypted/\n";

if ($mode === 'apply') {
    foreach ($sources as $source) {
        $from = $distDir . DIRECTORY_SEPARATOR . $source;
        $to = $base . DIRECTORY_SEPARATOR . $source;
        copyDirectory($from, $to);
        echo "Applied encrypted {$source}/\n";
    }
    echo "Live app/ and routes/ now use phpBolt. Restore with: php tools/phpbolt/encrypt.php --restore\n";
} else {
    echo "Dist only. Copy folders from encrypted/ when you are ready to ship.\n";
}

function encryptPhpFile(string $sourcePath, string $targetPath): void
{
    $key = randomKey(6);
    $contents = file_get_contents($sourcePath);
    if ($contents === false) {
        throw new RuntimeException("Cannot read {$sourcePath}");
    }

    if (str_contains($contents, 'bolt_decrypt(')) {
        copy($sourcePath, $targetPath);
        return;
    }

    $contents = preg_replace('/<\?php/', '', $contents, 1);
    $cipher = bolt_encrypt($contents, $key);
    $prepend = "<?php\nbolt_decrypt( __FILE__ , '{$key}'); return 0;\n##!!!##";
    if (file_put_contents($targetPath, $prepend . $cipher) === false) {
        throw new RuntimeException("Cannot write {$targetPath}");
    }
}

function randomKey(int $length): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $max = strlen($chars) - 1;
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $chars[random_int(0, $max)];
    }
    return $key;
}

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
        $target = $to . DIRECTORY_SEPARATOR . substr($file->getPathname(), strlen($from) + 1);
        if ($file->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0777, true);
            }
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

function countPlainPhpFiles(string $dir): int
{
    if (!is_dir($dir)) {
        return 0;
    }
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with(strtolower($file->getFilename()), '.php')) {
            $count++;
        }
    }
    return $count;
}
