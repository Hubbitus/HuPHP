<?php
declare(strict_types=1);

/**
* Database exceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
* @created ?2009-03-10 07:55 ver 1.0 to 1.1
*
* @uses BaseException
**/

namespace Hubbitus\HuPHP\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Database\IDatabase;

class DatabaseException extends BaseException {
	public ?IDatabase $db = null;

	public function __construct(string $message = '', int $code = 0, ?IDatabase $db = null, ?\Throwable $previous = null){
		$this->db = $db;
		parent::__construct($message, $code, $previous);
	}

	public function __clone(): void {
		// Exception base properties (message, code, previous) are cloned automatically
	}
}
