<?
/*-inc
include_once('Debug/debug.php');
include ('HuGetopt.php');
*/
/**
* @uses dump
* @uses HuGetopt
**/

$argv = array(
  0 => 'HuGetopt.php',
  1 => '-internet', //NOT option, Must handle properly and appear in nonoptions array
  2 => './replace_in_file.php',
  3 => '--what',
  4 => '/test/',
  5 => '--what/test2 of regexp/',
  6 => '--what=text test 3',	//= form
  7 => '--to',
  8 => 'QAZ',
  9 => '-i',
  10 => 'replace_in_file.test.text',
  11 => '/home/pasha/bin/filmInfoFormat.php',
  12 => '/home/pasha/bin/fsck.vfat.recode.php',
  13 => '/home/pasha/bin/mozila_get_extension_info.php',
  14 => '/home/pasha/bin/replace_in_file.php',
  15 => '/home/pasha/bin/SIM_history.php',
  16 => '-wFile' # Short without space
);
dump::a($argv);

$hgo = new HuGetopt(
	array(
		array('w', 'what', ':'),
//		array('w', 'what'),
		array('t', 'to', ':'),
		array('i', 'in-place'),
	)
);
$hgo->readPHPArgv()->parseArgs();
//dump::a($hgo->get('w')->Val);
dump::a($hgo->get('w')->Val->getArray());
dump::a($hgo->get('i')->Val->getArray());
dump::a($hgo->getNonOpts());
?>