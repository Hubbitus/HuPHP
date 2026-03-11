#!/usr/bin/php
<?php
declare(strict_types=1);

use Hubbitus\HuPHP\Debug\Dump;

/*
$p = new Phar('HuDebug.phar', 0, 'HuDebug.phar');
dump::a($p);

	foreach ($p as $file) {
	dump::a($file->getFileName());
	dump::a($file->isCompressed());
	dump::a($file->isCompressedBZIP2());
	dump::a($file->isCompressedGZ());
	}
*/

// Load the PHAR file using path relative to this script
$pharPath = __DIR__ . '/build/HuPHP.phar';

if (!\file_exists($pharPath)) {
	\fwrite(STDERR, "Error: PHAR file does not exist at path: " . $pharPath . "\n");
	exit(1);
}

// Attempt to require the PHAR file and handle any errors
try {
	require_once($pharPath);

	// The PHAR should have the autoloader built-in, so now we can use the classes
	Dump::a('Just test');
	$testString = 'some';
	Dump::a($testString);
	echo "PHAR test completed successfully!" . PHP_EOL;
} catch (Throwable $e) {
	\fwrite(STDERR, "Error loading or executing PHAR: " . $e->getMessage() . "\n");
	exit(1);
}
