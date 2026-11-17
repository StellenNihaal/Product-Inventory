<?php

/**
 * Quick phpBolt smoke test.
 * Usage: php tools/phpbolt/test-phpbolt.php
 */

if (!extension_loaded('bolt') || !function_exists('bolt_encrypt') || !function_exists('bolt_decrypt')) {
    fwrite(STDERR, "FAIL: phpBolt extension not loaded.\n");
    exit(1);
}

$key = 'test12';
$plain = " echo 'PHPBOLT_OK';";
$cipher = bolt_encrypt($plain, $key);
$file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpbolt_smoke.php';
file_put_contents(
    $file,
    "<?php\nbolt_decrypt( __FILE__ , '{$key}'); return 0;\n##!!!##" . $cipher
);

ob_start();
include $file;
$output = trim(ob_get_clean());

echo "extension: OK\n";
echo "encrypt:   OK (" . strlen($cipher) . " bytes)\n";

if ($output !== 'PHPBOLT_OK') {
    fwrite(STDERR, "FAIL: decrypt output was '{$output}'\n");
    exit(1);
}

echo "decrypt:   OK\n";
echo "result:    PHPBOLT_OK\n";
echo "ALL TESTS PASSED\n";
