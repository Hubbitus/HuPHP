#!/usr/bin/php
<?php
declare(strict_types=1);

// The script is run from @tools directory, so go up to main directory
require_once __DIR__ . '/../../vendor/autoload.php';

use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\Debug\Backtrace;

$someVar = 'Just test';
Dump::a($someVar);

$arr = ['oen', 'two', 'three' => ['three-one', ['three-two']]];
Dump::a($arr);

function f() {
	$bt = Backtrace::create();
	$bt->printFormat();
}
\f();

echo "Raw test completed successfully!\n";
