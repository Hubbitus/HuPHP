<?
/**
* Toolkit of small functions as "macroses".
*
* Example and test toolkit for 
*
* @package Macroses
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

include_once('autoload.php');

function test_EMPTY_NONEMPTY_STR(&$var){
dump::a($var, 'Original value');
//dump::a(EMPTY_STR('', $var, 'DEFAULT'));
//dump::a(EMPTY_STR('', 0, $var, 'DEFAULT'));

//dump::a(EMPTY_STR($var, 'DEFAULT ($var is empty)'));
dump::a(EMPTY_STR($var));
dump::a(NON_EMPTY_STR($var, '<', '>'));

dump::a('===================================');
}#f test_EMPTY_NONEMPTY_STR

$t = true;
test_EMPTY_NONEMPTY_STR($t);	# ''; ''
$t = false;
test_EMPTY_NONEMPTY_STR($t);	# ''; ''
$t = null;
test_EMPTY_NONEMPTY_STR($t);	# ''; ''
$t = array(1,2,3);
test_EMPTY_NONEMPTY_STR($t);	# 'Array(3)'; '<Array(3)>'
$t = array();
test_EMPTY_NONEMPTY_STR($t);	# 'Array(0)'; '<Array(0)>'
$t = '';
test_EMPTY_NONEMPTY_STR($t);	# ''; ''
$t = ' ';
test_EMPTY_NONEMPTY_STR($t);	# ' '; '< >'
$t = '   ';
test_EMPTY_NONEMPTY_STR($t);	# '  '; '<  >'
$t = 0;
test_EMPTY_NONEMPTY_STR($t);	# '0'; '<0>'
$t = '0';
test_EMPTY_NONEMPTY_STR($t);	# '0'; '<0>'
?>