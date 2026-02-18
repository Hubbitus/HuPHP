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
	public $filename = '';
	protected $fullPath = '';

	public function __construct($message = '', $filename = '', int $code = 0, ?\Throwable $previous = null){
		$this->filename = $filename;
		$this->fullPath = $filename;
		parent::__construct($message, $code, $previous);
	}

	public function __clone() {
		// Clone is allowed, Exception handles message/code/previous automatically
	}
	
	/**
	* Get the full path
	* @return string
	**/
	public function getFullPath(): string {
		return $this->fullPath;
	}

	// custom string representation of object
	public function __toString(): string {
		return __CLASS__ . ": [{$this->fullPath}]: {$this->message}\n";
	}
}

