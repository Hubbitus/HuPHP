<?php
declare(strict_types=1);

/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-10-23 16:43
**/

namespace Hubbitus\HuPHP\Macroses;

/**
* Unicode variant of ucfirst!
* Idea got from http://php.net/manual/en/function.ucfirst.php#87133 but its implementation
* is very long and hard without reason.
*
* @uses mb_strtoupper
* @param        string  $str    String to process
* @param        string=UTF-8    $enc
**/
function unicode_ucfirst($str, $enc = 'UTF-8'): ?string {
    if (empty($str)) {
        return '';
    }

    return \preg_replace_callback('/^./u', fn(array $matches): mixed => \mb_strtoupper($matches[0], $enc), $str);
}
