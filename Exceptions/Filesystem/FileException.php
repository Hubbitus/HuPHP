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
	public string $filename = '';
	protected string $fullPath = '';

	public function __construct(string $message = '', string $filename = '', int $code = 0, ?\Throwable $previous = null){
		$this->filename = $filename;
		$this->fullPath = $filename ?? '';
		parent::__construct($message, $code, $previous);
	}

	/**
	* Get the full path
	* @return string
	**/
	public function getFullPath(): string {
		return $this->fullPath;
	}

	/**
	* Clone the exception including custom properties
	**/
	public function __clone(): void {
		// Exception base properties (message, code, previous) are cloned automatically
		// Custom properties need explicit handling if they are objects
	}

	// custom string representation of object
	public function __toString(): string {
		return __CLASS__ . ": [{$this->fullPath}]: {$this->message}\n";
	}
}

