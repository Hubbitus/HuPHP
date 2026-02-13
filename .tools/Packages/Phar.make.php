#!/usr/bin/php
<?php
declare(strict_types=1);

/**
* Debug and backtrace toolkit.
* Utility to generate one Phar-file suitable for debugging, contained all needed dependencies.
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

require('config.php');

// Load macro functions
require_once __DIR__ . '/../../macroses/REQUIRED_NOT_NULL.php';
require_once __DIR__ . '/../../macroses/REQUIRED_VAR.php';
require_once __DIR__ . '/../../macroses/EMPTY_VAR.php';
require_once __DIR__ . '/../../macroses/EMPTY_STR.php';
require_once __DIR__ . '/../../macroses/EMPTY_INT.php';

// Initialize autoloader for the project
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    // If class starts with Hubbitus\HuPHP, strip it for file path calculation
    if (strpos($class, 'Hubbitus\\HuPHP\\') === 0) {
        $relative_class = substr($class, 14); // Length of 'Hubbitus\HuPHP\'
    } else {
        $relative_class = $class;
    }
    
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    $file = __DIR__ . '/../../' . $file;
    
    if (file_exists($file)) {
        require_once $file;
    }
});

	// create a new phar - phar.readonly must be 0 in php.ini
	// phar.readonly is enabled by default for security reasons.
	// On production servers, PHAR need never be created, only executed.
	if (Phar::canWrite()) {
		$p = new Phar(\FILEPATH_PHAR, 0, \FILENAME_PHAR);

//		$includes[] = BASE_DIR . '/Debug/_HuFormat.defaults/backtrace::printout.php';
		// Generate list of files to include by scanning the directory structure
		$files = new RecursiveIteratorIterator(
		    new RecursiveDirectoryIterator(__DIR__ . '/../../', RecursiveDirectoryIterator::SKIP_DOTS)
		);

		$includes = [];
		foreach ($files as $file) {
		    if ($file->getExtension() === 'php') {
		        $path = $file->getPathname();
		        // Convert absolute path to relative path
		        $relativePath = str_replace(__DIR__ . '/../../', '', $path);
		        
		        // Skip certain directories
		        if (!preg_match('/^(.tools|.git|vendor)/', $relativePath)) {
		            $includes[] = $relativePath;
		        }
		    }
		}

		$i = 0;
		foreach ($includes as $inc){
			echo ++$i . ") Process [$inc]\n";

			//Replace all includes and requires on Phar!
			$file = new \Hubbitus\HuPHP\Filesystem\FileInMemory(__DIR__ . '/../../' . $inc);
			$file->loadContent();
			$re = new \Hubbitus\HuPHP\RegExp\RegExpPcre(
				//		\\1		 \\2		\\3		\\4 \\5
				'/(include|require)(_once)?\s*(\()?\s*(\'|")(.*)\\4\s*\\3?\)/', // Do NOT anchor to ;! It is may appear inner string too (in conditions f.e.)
				$file->getBLOB(),
				'\\1\\2(\\4phar://' . FILENAME_PHAR . '/\\5\\4)'
			);
			$p[$inc] = $re->replace();
		}

		//With BZIP2 compression you may have trouble on other systems, where Phar not working, and load 100% of CPU
		//And futhermore, GZIP give me _MORE_ compression than bzip2 (39017 byte opposite 41110)
		try{//Try GZIP
			$p->compressFiles(Phar::GZ);
		}
		catch (BadMethodCallException $bmce){//Not supported too. So, stay uncompressed.
			fwrite(STDERR, 'Warning: GZip compression is not supported too.'."\n");
		}

		$p->setStub('<?php Phar::mapPhar("' . FILENAME_PHAR . '"); include_once("phar://' . FILENAME_PHAR . '/autoload.php"); __HALT_COMPILER(); ?>');
		fwrite(STDERR, 'PHAR file created successfully: ' . FILEPATH_PHAR . PHP_EOL);
	} else {
		fwrite(STDERR, 'Warning: Phar write is not possible! Create a new phar - phar.readonly must be 0 in php.ini. Option phar.readonly is enabled by default for security reasons. On production servers, Phars need never be created, only executed.' . PHP_EOL);
		// Exit gracefully instead of throwing exception
		exit(0);
	}
