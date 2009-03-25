#!/usr/bin/php
<?
/**
* Debug and backtrace toolkit.
* Utility to generate one Phar-file suitable fo debbuging, contained all needed dependencies.
* When Phar is not accessible consider use one.Debug.php It has less futures (like compression etc...) but useful in most cases.
*
* @package Debug
* @subpackage Phar.Debug
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-25 15:07 ver 1.0 to 1.1
*	- After split file_base to 2 childs switch there use file_inmem.
**/

include_once('../debug.php');
include_once('Filesystem/file_inmem.php');

define('BASE_DIR', '/var/www/_SHARED_/');

	// create a new phar - phar.readonly must be 0 in php.ini
	// phar.readonly is enabled by default for security reasons.
	// On production servers, Phars need never be created,
	// only executed.
	if (Phar::canWrite()) {
	$p = new Phar(dirname(__FILE__) . '/HuDebug.phar', 0, 'HuDebug.phar');

	$includes = get_included_files();
	unset($includes[0]); //Self not needed

	$includes[] = BASE_DIR . 'Debug/_HuFormat.defaults/backtrace::printout.php';

	$i=0;
		foreach ($includes as $inc){
		echo ++$i . ") Process $inc\n";
//			if ($inc == __FILE__) continue;

		//Replace all includes and requires on Phar!
		$file = new file_inmem($inc);
		$file->loadContent();
		$re = new RegExp_pcre(
			#		\\1		 \\2		\\3		\\4 \\5
			'/(include|require)(_once)?\s*(\()?\s*(\'|")(.*)\\4\s*\\3?\)/',	#Do NOT anchor to ;! It is may appear inner string too (in conditions f.e.)
			$file->getBLOB(),
			'\\1\\2(\\4phar://HuDebug.phar/\\5\\4)'
		);
		$p[substr($inc, strlen(BASE_DIR))] = $re->replace();
		}

//With BZIP2 compression you may have trouble on other systems, whree Phar not working, and load 100% of CPU
//And futhermore, GZIP give me _MORE_ compression than bzip2 (39017 byte opposite 41110)
//?		try{
//?		$p->compressFiles(Phar::BZ2);
//?		}
//?		catch (BadMethodCallException $bmce){//Not Supported BZIP2.
//?		fwrite(STDERR, 'Warning: Bzip2 compression is not supported. Trying GZip'."\n");
			try{//Try GZIP
#DO NOT COMPRESS!!
			$p->compressFiles(Phar::GZ);
			}
			catch (BadMethodCallException $bmce){//Not supported too. So, stay uncompressed.
			fwrite(STDERR, 'Warning: GZip compression is not supported too.'."\n");
			}
//?		}

/*
	$p->setMetaData(array('bootstrap' => 'Debug/debug.php'));
	// set the loader stub
	$p->setStub('<?
$p = new Phar(__FILE__);
$m = $p->getMetaData();
require "phar://" . __FILE__ . "/" . $m["bootstrap"];
__HALT_COMPILER();');
*/

	$p->setStub('<? Phar::mapPhar("HuDebug.phar"); include_once("phar://HuDebug.phar/Debug/debug.php"); __HALT_COMPILER(); ?>');
/*	$p->setStub('<? Phar::mapPhar("HuDebug.phar"); include_once("phar://HuDebug.phar/Debug/debug.php"); __HALT_COMPILER(); ?>'); */
/*	$p->setStub("#!/usr/bin/php\n<? Phar::mapPhar('HuDebug.phar'); include_once('phar://HuDebug.phar/Debug/debug.php'); echo 'This is Example self-executable Phar!!!'; __HALT_COMPILER(); ?>"); */
/*	$p->setStub('<? echo "Stub used\n"; Phar::mapPhar("HuDebug.phar"); include_once("phar://HuDebug.phar/Debug/debug.php"); __HALT_COMPILER(); ?>'); */
	}
	else{
	throw new Exception('Phar write is not possible!');
	}
?>