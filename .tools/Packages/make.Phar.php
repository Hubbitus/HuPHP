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
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-25 15:07 ver 1.0 to 1.1
*
* @uses file_inmem
**/

require('autoload.php');
require('config.php');

	// create a new phar - phar.readonly must be 0 in php.ini
	// phar.readonly is enabled by default for security reasons.
	// On production servers, Phars need never be created, only executed.
	if (Phar::canWrite()) {
		$p = new Phar(FILEPATH_PHAR, 0, FILENAME_PHAR);

//		$includes[] = BASE_DIR . '/Debug/_HuFormat.defaults/backtrace::printout.php';
		new HuArray();// Any class to load autoinclude map
		$includes = array_unique(array_values($GLOBALS['__CONFIG']['__autoload_map']));

		$i = 0;
		foreach ($includes as $inc){
			echo ++$i . ") Process [$inc]\n";

			//Replace all includes and requires on Phar!
			$file = new file_inmem(BASE_DIR . '/' . $inc);
			$file->loadContent();
			$re = new RegExp_pcre(
				//		\\1		 \\2		\\3		\\4 \\5
				'/(include|require)(_once)?\s*(\()?\s*(\'|")(.*)\\4\s*\\3?\)/', // Do NOT anchor to ;! It is may appear inner string too (in conditions f.e.)
				$file->getBLOB(),
				'\\1\\2(\\4phar://' . FILENAME_PHAR . '/\\5\\4)'
			);
			$p[$inc] = $re->replace();
		}

//With BZIP2 compression you may have trouble on other systems, whree Phar not working, and load 100% of CPU
//And futhermore, GZIP give me _MORE_ compression than bzip2 (39017 byte opposite 41110)
//?		try{
//?		$p->compressFiles(Phar::BZ2);
//?		}
//?		catch (BadMethodCallException $bmce){//Not Supported BZIP2.
//?		fwrite(STDERR, 'Warning: Bzip2 compression is not supported. Trying GZip'."\n");
		try{//Try GZIP
//			$p->compressFiles(Phar::GZ);
		}
		catch (BadMethodCallException $bmce){//Not supported too. So, stay uncompressed.
			fwrite(STDERR, 'Warning: GZip compression is not supported too.'."\n");
		}
//?		}

		$p->setStub('<? Phar::mapPhar("' . FILENAME_PHAR . '"); include_once("phar://' . FILENAME_PHAR . '/autoload.php"); __HALT_COMPILER(); ?>');
	}
	else{
		throw new Exception('Phar write is not possible! Create a new phar - phar.readonly must be 0 in php.ini. Option phar.readonly is enabled by default for security reasons. On production servers, Phars need never be created, only executed.');
	}
?>