#!/usr/bin/php
<?
/**
* Debug and backtrace toolkit.
* Utility to generate one file suitable fo debbuging, contained all needed dependencies.
* This useful where Phar is not accessible. In other cases Phar should bee used - it also provide additional futures like compression etc...
*
* @package Debug
* @subpackage one.Debug
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-25 15:07 ver 1.0 to 1.1
*
* @uses file_inmem
**/

require('autoload.php');
require('config.php');

//$includes = get_included_files();
//unset($includes[0]); //Self

//Manualy include
//array_push($includes, BASE_DIR . 'Debug/_HuFormat.defaults/backtrace::printout.php');

new HuArray();// Any class to load autoinclude map
$includes = array_unique(array_values($GLOBALS['__CONFIG']['__autoload_map']));
sort($includes); // Renumerate keys

$filesIncluded = array();

$res = new file_inmem(FILEPATH_ONE);
$res->appendString('<?
/** This is automaticaly generated file. Please, do not edit it! Instead use scripts from .tools directory to regenerate. **/
?>');

	// Backward each
	for ($i = count($includes) - 1; $i >= 0 and $inc = $includes[$i]; $i--){
//		if (!in_array($inc, $filesIncluded)){
			echo $i . ") Process [$inc]\n";

			$file = new file_inmem(BASE_DIR . '/' . $inc);
			$file->loadContent();
			$re = new RegExp_pcre(
				array(
					'@\(include_once\(\'Debug/_HuFormat.defaults/backtrace::printout.php\'\)\)@' //Special case
					//		\\1		 \\2		\\3		\\4 \\5
					,'/(include|require)(_once)?\s*(\()?\s*(\'|")(.*)\\4\s*\\3?\);/',
				)
				,$file->getBLOB()
				,array(
					'/*-One- \\0 ++>true*/ (false)'
					,''
				)
			);
			$res->appendString($re->replace());
			$filesIncluded[] = $inc;
//		}
	}

$res->writeContent();
?>