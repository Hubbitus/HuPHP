<?php
declare(strict_types=1);

/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @subpackage _count
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

namespace Hubbitus\HuPHP\Macroses;

use Hubbitus\HuPHP\Exceptions\HaltException;

/**
* Terminate program with message $message if count exceeded $count.
* Throws HaltException instead of calling exit() for testability.
* @param integer        $count  Count compare to. {@see hit_count()}
* @param string $message=''     Optional message to halt with.
*
* @return       void
* @throws HaltException
**/
function exit_count($count, $message=''): void {
        if (true === hit_count($count)) {
                throw new HaltException($message, 0);
        }
}

/**
* Calc hit of invokes and return === true if it equals to $count, else return number of current hit.
* @param integer        $count Count to compare.
*
* @return       bool|integer
**/
function hit_count($count){
        static $_count = 0;
        if (++$_count == $count) return true;
        else return $_count;
}