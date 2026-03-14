<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

/**
* VariableStream stream wrapper. Manipulate stream 'var://varName' as file, where content is $varName.
* Very useful example from http://www.php.net/manual/ru/function.stream-wrapper-register.php as base for implementation
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-06-16 14:33
*
* @example
* ```php
* $var = '';
* $fp = fopen('var://var', 'r+');
* fwrite($fp, "line1\n");
* fwrite($fp, "line2\n");
* fwrite($fp, "line3\n");
* rewind($fp);
* while (!feof($fp)) {
*     echo fgets($fp);
* }
* fclose($fp);
* var_dump($var);
* ```
**/
class VariableStream {
	public int $position;
	public string $varname;

	/**
	* Stream context (set by PHP stream wrapper API)
	*
	* Cannot use `?\resource` type hint due to PHP stream wrapper API limitations:
	* PHP assigns $context before class is fully initialized, causing TypeError.
	*
	* @var ?resource
	**/
	public $context = null;

	/**
	* Open stream.
	*
	* @param string $path Stream path
	* @param string $mode Open mode
	* @param int $options Stream options
	* @param string|null $opened_path Opened path
	**/
	public function streamOpen(string $path, string $mode, int $options, ?string &$opened_path): bool {
		$url = \parse_url($path);
		$this->varname = $url['host'];
		$this->position = 0;

		// Initialize variable if needed
		if (!isset($GLOBALS[$this->varname])) {
			$GLOBALS[$this->varname] = '';
		}

		// Clear the variable for write modes
		if ($mode === 'w' || $mode === 'w+') {
			$GLOBALS[$this->varname] = '';
		}

		// For append mode, seek to end
		if ($mode === 'a' || $mode === 'a+') {
			$this->position = \strlen($GLOBALS[$this->varname]);
		}

		return true;
	}

	/**
	* Read from stream.
	*
	* @param int $count Number of bytes to read
	**/
	public function streamRead(int $count): string {
		$var = $GLOBALS[$this->varname] ?? '';
		$ret = \substr($var, $this->position, $count);
		$this->position += \strlen($ret);
		return $ret;
	}

	/**
	* Write to stream.
	*
	* @param string $data Data to write
	* @return int
	**/
	public function streamWrite(string $data): int {
		$var = $GLOBALS[$this->varname] ?? '';
		$left = \substr($var, 0, $this->position);
		$right = \substr($var, $this->position + \strlen($data));
		$GLOBALS[$this->varname] = $left . $data . $right;
		$this->position += \strlen($data);
		return \strlen($data);
	}

	/**
	* Get current position.
	*
	* @return int
	**/
	public function streamTell(): int {
		return $this->position;
	}

	/**
	* Check if end of stream.
	*
	* @return bool
	**/
	public function streamEof(): bool {
		$var = $GLOBALS[$this->varname] ?? '';
		return $this->position >= \strlen($var);
	}

	/**
	* Get stream statistics.
	*
	* @return array<int, mixed>
	**/
	public function streamStat(): array {
		return [];
	}

	/**
	* Seek to position.
	*
	* @param int $offset Offset
	* @param int $whence Seek mode
	**/
	public function streamSeek(int $offset, int $whence): bool {
		$var = $GLOBALS[$this->varname] ?? '';
		switch ($whence) {
			case SEEK_SET:
				if ($offset >= 0 && $offset < \strlen($var)) {
					$this->position = $offset;
					return true;
				} else {
					return false;
				}

			case SEEK_CUR:
				if ($offset >= 0) {
					$this->position += $offset;
					return true;
				} else {
					return false;
				}

			case SEEK_END:
				if (\strlen($var) + $offset >= 0) {
					$this->position = \strlen($var) + $offset;
					return true;
				} else {
					return false;
				}

			default:
				return false;
		}
	}

	/**
	* Register the 'var' stream wrapper if not already registered.
	* This method is called automatically when the class is loaded,
	* but can also be called explicitly for testing purposes.
	*
	* @return bool True if wrapper was registered, false if already exists
	**/
	public static function registerStreamWrapper(): bool {
		if (!\in_array('var', \stream_get_wrappers(), true)) {
			return \stream_wrapper_register('var', VariableStream::class);
		}
		return false;
	}
}

// Auto-register stream wrapper on class load
VariableStream::registerStreamWrapper()
	or die('Failed to register protocol');
