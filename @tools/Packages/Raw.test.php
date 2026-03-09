#!/usr/bin/php
<?php
declare(strict_types=1);

// The script is run from @tools directory, so go up to main directory
require_once __DIR__ . '/../../vendor/autoload.php';

use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\Debug\Backtrace;

Dump::a('Just test');

function f(){
    $bt = Backtrace::create();
    $bt->printFormat();
}
f();

echo "Raw test completed successfully!\n";
