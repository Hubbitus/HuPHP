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
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-25 15:07 ver 1.0 to 1.1
*	- Due to the file-API change, writeContents call adjusted to writeContent
*	- After split file_base to 2 childs switch there use file_inmem.
**/

include_once('../debug.php');
include_once('Filesystem/file_inmem.php');

define('BASE_DIR', '/var/www/_SHARED_/');

$includes = get_included_files();
unset($includes[0]); //Self

//Manualy include
array_push($includes, BASE_DIR . 'Debug/_HuFormat.defaults/backtrace::printout.php');

$filesIncluded = array();

$res = new file_inmem('one.Debug.php');
$res->appendString('<?
/** This is automaticaly generated file. Please, do not edit it! **/
?>');

//dump::a($includes); dump::a(count($includes)); exit();

	#Backward each
	for ($i=count($includes); $i>=1 and $inc = $includes[$i]; $i--){
dump::a($inc);
// continue;
		if (!in_array($inc, $filesIncluded)){
		//Replace all includes and requires on Phar!
		$file = new file_inmem($inc);
		$file->loadContent();
		$re = new RegExp_pcre(
			array(
				'#\(include_once\(\'Debug/_HuFormat.defaults/backtrace::printout.php\'\)\)#' #Special case
				#		\\1		 \\2		\\3		\\4 \\5
				,'/(include|require)(_once)?\s*(\()?\s*(\'|")(.*)\\4\s*\\3?\);/',
			)
			,$file->getBLOB()
//			,'\\1\\2(\'phar://HuDebug.phar/\\5\');'
			,array(
				'/*-One- \\0 ++>true*/ (false)'
				,''
			)
		);
	//	$p[substr($inc, strlen(BASE_DIR))] = $re->replace();
		$res->appendString($re->replace());
		$filesIncluded[] = $inc;
		}
	}

$res->writeContent();
?>