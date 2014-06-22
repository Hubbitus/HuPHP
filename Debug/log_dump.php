<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage log_dump
* @version 2.2b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-29 17:35
**/

include_once('Debug/dump_utils.php'); //Function used. Must be included explicit.
/**
* @uses log_dump()
**/

/**
* Log dump. Useful to return string for file-write.
*
* @param	mixed $var Variable (or scalar) to dump.
* @param string|false	$header. Header to prepend dump of $var.
* @param boolean		$return If true - return result as string instead of echoing.
* @return string|void	Depend on parameter $return
**/
function log_dump($var, $header = false, $return = true){
	$ret = '';
	if ($header) $ret .= $header .':'; //As is and only explicitly given, without any magic
	$ret .= dump_utils::transformCorrect_print_r(print_r($var, true))."\n";
	if ($return) return $ret;
	else echo $ret;
}#f log_dump

	if (
		!class_exists('dump')
		or
			(
			!defined('DUMP_DO_NOT_DEFINE_STUMP_DUMP')
			and DUMP_DO_NOT_DEFINE_STUMP_DUMP
			)
	){
		class dump extends dump_utils{
			function log($var, $header = false, $return = true){
			return log_dump($var, $header = false, $return = true);
			}
		};
	}
?>