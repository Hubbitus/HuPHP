<?
/**
* Debug and backtrace toolkit.
* @package Debug
* @subpackage Dump-utils
* @version 2.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-06-26 03:58
*	- Add in transformCorrect_print_r transformation empty array into 1 string ( /Array(0){\s+}/ -> Array(0){} )
**/

class dump_utils{
	/**
	* Transform string, which is text-representation of requested var into more well formated form.
	* print_r variant
	* @param string $dump String returned by print_r
	* @return string. Transformed, well-formated.
	**/
	static public function transformCorrect_print_r($dump){
	return
		trim(
			preg_replace(
				array(
					'/Array\n\s*\(/',
					'/Object\n\s*\(/',
					'/\["(.+)"\]=>\n /',
					'/Array(0){\s+}/',
				),
				array(
					'Array(',
					'Object(',
					'[\1]=>',
					'Array(0){}',
				),
				$dump
			)
		);
	}#m transformCorrect_print_r

	/**
	* Transform string, which is text-representation of requested var into more well formated form.
	* var_dump variant
	* @param string $dump String returned by var_dump
	* @return string. Transformed, well-formated.
	**/
	static public function transformCorrect_var_dump($dump){
	return
		trim(/* For var_dump variant */
			preg_replace(
				array(
					'/array(\(\d+\))\s+({)/i',
					'/Object\n\s*\(/',
					'/\["?(.+?)"?\]=>\n\s*/',
				),
				array(
					'Array\1\2',
					'Object(',
					'[\1] => ',
				),
				$dump
			)
		);
	}#m transformCorrect_var_dump
}; #c dump_utils
?>