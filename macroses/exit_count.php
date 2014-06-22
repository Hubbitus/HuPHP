<?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @subpackage _count
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Terminate program with message $message if count exceeded $count.
* @param integer	$count	Count compare to. {@see hit_count()}
* @param string	$message=''	Optional message to die with.
*
* @return	void
**/
function exit_count($count, $message=''){
	if (true === hit_count($count)) exit($message);
}

/**
* Calc hit of invokes and return === true if it equals to $count, else return number of current hit.
* @param integer	$count Count to compare.
*
* @return	bool|integer
**/
function hit_count($count){
static $_count = 0;
	if (++$_count == $count) return true;
	else return $_count;
}
?>