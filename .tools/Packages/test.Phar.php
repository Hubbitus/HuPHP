#!/usr/bin/php
<?
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

dump::a('Just test');

dump::a(ini_get('include_path'));
dump::a(ini_set('include_path', '---')); // For the clear results in the experiment!
dump::a(ini_get('include_path'));

function f(){
	backtrace::create()->printout();
}

f();


?>