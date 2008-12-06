#!/usr/bin/php
<?
include_once('../debug.php');

define('BASE_DIR', '/var/www/_SHARED_/');

$includes = get_included_files();
unset($includes[0]); //Self

//Manualy include
array_push($includes, BASE_DIR . 'Debug/_HuFormat.defaults/backtrace::printout.php');

$filesIncluded = array();

$res = new file_base('one.Debug.php');
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
		$file = new file_base($inc);
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

$res->writeContents();
?>