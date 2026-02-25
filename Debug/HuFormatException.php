<?php
declare(strict_types=1);

/**
* Exception for HuFormat errors.
*
* @package Debug
* @subpackage HuFormat
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Exceptions\Variables\VariableException;

/**
* Exception thrown by HuFormat class.
**/
class HuFormatException extends VariableException {}
