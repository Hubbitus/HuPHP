<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions;

/**
* BaseException
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
**/
class BaseException extends \Exception {
	// $pos = false - at end, else - in beginning
	public function ADDMessage(string $addonMessage, bool $pos = false): void {
		if (!$pos) {
			$this->message .= $addonMessage;
		}
		else {
			$this->message = $addonMessage . $this->message;
		}
	}
}
