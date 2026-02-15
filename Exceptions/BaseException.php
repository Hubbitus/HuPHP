<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions;

/**
* BaseException
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

class BaseException extends \Exception {
	// $pos = false - at end, else - in beginning
	public function ADDMessage($addonMessage, $pos = false): void {
		if (!$pos) $this->message .= $addonMessage;
		else $this->message = $addonMessage . $this->message;
	}
}
