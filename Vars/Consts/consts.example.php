<?
/**
*
* @package Vars
* @subpackage Consts
* @version 1.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/*-inc
include('Vars/Consts/consts.php');
include_once('Debug/debug.php');
*/
include('autoload.php');
/**
* @uses consts
* @uses dump
**/

/*
$constants = get_defined_constants(true);
print_r($constants['session']);
exit(); BUG!!! http://bugs.php.net/47549
*/


#dump::a(consts::get_regexp('tidy'));
#dump::A(consts::get_regexp('', '/TYPE/i'));
#dump::a(consts::get_regexp('tidy', '/TYPE/i'));

#dump::a(consts::get('TIDY_NODETYPE_STARTEND'));

#dump::a(TIDY_NODETYPE_STARTEND);
#dump::a(consts::getNameByValue(TIDY_NODETYPE_STARTEND, '', '/^TIDY/', true));
#dump::a(consts::getNameByValue(TIDY_NODETYPE_STARTEND, '', '/^TIDY/', false));
dump::a(consts::getNameByValue(366, '', '/^TIDY/', false));
?>