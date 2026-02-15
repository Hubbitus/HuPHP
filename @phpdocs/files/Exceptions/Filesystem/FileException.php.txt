<?php
declare(strict_types=1);

/**
* FileSystem Exceptions
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

namespace Hubbitus\HuPHP\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\BaseException;

class FileException extends BaseException {
	protected $fullPath = '';

	public function __construct($message, $fullPath){
		$this->fullPath = $fullPath;
		parent::__construct($message);
	}

	// custom string representation of object
	public function __toString(): string {
		return __CLASS__ . ": [{$this->fullPath}]: {$this->message}\n";
	}
}
