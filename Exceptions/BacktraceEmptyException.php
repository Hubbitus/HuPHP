<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException;

class BacktraceEmptyException extends VariableEmptyException {
	public function __construct(?string $message = null, ?int $code = 0) {
		// Create empty Backtrace for parent constructor
		$backtrace = new Backtrace();
		parent::__construct($backtrace, null, $message, $code);
	}
}
