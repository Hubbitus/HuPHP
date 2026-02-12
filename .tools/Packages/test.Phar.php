#!/usr/bin/php
<?php
declare(strict_types=1);

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

//var_dump(ini_get('include_path'));exit();
//include_once('phar://HuDebug.phar/Debug/debug.php');

define('AUTOLOAD_DEBUG', true);

require('config.php');
require(FILEPATH_PHAR);

Dump::a('Just test');

Dump::a(ini_get('include_path'));
Dump::a(ini_set('include_path', '---')); // For the clear results in the experiment!
Dump::a(ini_get('include_path'));

function f(){
	Backtrace::create()->printout();
}

f();
?>