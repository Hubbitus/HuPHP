<?php
/**
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\Vars\Consts\Consts;

/*
$constants = get_defined_constants(true);
print_r($constants['session']);
exit(); BUG!!! http://bugs.php.net/47549
*/

Dump::a(Consts::getByRegexp('tidy'));
Dump::A(Consts::getByRegexp('', '/TYPE/i'));
Dump::a(Consts::getByRegexp('tidy', '/TYPE/i'));

Dump::a(Consts::get('TIDY_NODETYPE_STARTEND'));

Dump::a(\TIDY_NODETYPE_STARTEND);
Dump::a(Consts::getNameByValue(\TIDY_NODETYPE_STARTEND, '', '/^TIDY/', true));
Dump::a(Consts::getNameByValue(\TIDY_NODETYPE_STARTEND, '', '/^TIDY/', false));
Dump::a(Consts::getNameByValue(366, '', '/^TIDY/', false));
