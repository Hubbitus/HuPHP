<?
/**
* 
* @package Vars
* @subpackage Consts
* @version 1.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

include('consts.php');
include_once('debug.php');

dump::a(consts::get_regexp('tidy'));
dump::A(consts::get_regexp('', '/TYPE/i'));
dump::a(consts::get_regexp('tidy', '/TYPE/i'));

dump::a(consts::get('TIDY_NODETYPE_STARTEND'));
?>