<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions;

/**
* ProcessException
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

class ProcessException extends BaseException {
public $state = null;

	public function __construct($message = null, $code = 0, $pr = null) {
		$this->state = $pr;

		// make sure everything is assigned properly
		if (is_object($message)) {
			parent::__construct(json_encode($message) ?? 'Object message', $code);
		} elseif (is_array($message)) {
			parent::__construct(json_encode($message) ?? 'Array message', $code);
		} else {
			parent::__construct((string)$message, $code);
		}
	}
}
