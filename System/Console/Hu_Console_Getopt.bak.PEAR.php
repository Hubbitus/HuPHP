<?
/**
* @deprecated Now use HuGetopt to any purpose! Stay for backward compatibylity.
* RegExp manupulation.
* @package HuGetopt
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

require_once 'Console/Getopt.php';	#PEAR

class Hu_Console_Getopt extends Console_Getopt{
public static function SwapKeysAndNumbers($options){
$ret = array (
	0 => array(),	#To be filling
	1 => $options[1]	#Just copy
);

	foreach ($options[0] as $key => $val){
	$ret[0][$options[0][$key][0]] = array(0 => $key, 1 => $options[0][$key][1]);
	}
return $ret;
}#m SwapKeysAndNumbers

}#c Hu_Console_Getopt
?>