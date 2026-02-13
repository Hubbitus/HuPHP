<?php
/**
* @package Vars
* @subpackage Consts
* @version 1.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @uses consts
* @uses dump
**/

include('autoload.php');

/*
$constants = get_defined_constants(true);
print_r($constants['session']);
exit(); BUG!!! http://bugs.php.net/47549
*/

Dump::a(Consts::get_regexp('tidy'));
Dump::A(Consts::get_regexp('', '/TYPE/i'));
Dump::a(Consts::get_regexp('tidy', '/TYPE/i'));

Dump::a(Consts::get('TIDY_NODETYPE_STARTEND'));

Dump::a(TIDY_NODETYPE_STARTEND);
Dump::a(Consts::getNameByValue(TIDY_NODETYPE_STARTEND, '', '/^TIDY/', true));
Dump::a(Consts::getNameByValue(TIDY_NODETYPE_STARTEND, '', '/^TIDY/', false));
Dump::a(Consts::getNameByValue(366, '', '/^TIDY/', false));
