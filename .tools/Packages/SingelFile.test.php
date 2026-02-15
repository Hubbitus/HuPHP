#!/usr/bin/php
<?php
declare(strict_types=1);

use Hubbitus\HuPHP\Debug\Dump;

require('config.php');

if (!file_exists(FILEPATH_ONE)) {
    fwrite(STDERR, "Error: Single file does not exist at path: " . FILEPATH_ONE . "\n");
    exit(1);
}

require(FILEPATH_ONE);

require_once __DIR__ . '/../../vendor/autoload.php';
Dump::a('Just test');
echo "Single file test completed successfully!\n";
