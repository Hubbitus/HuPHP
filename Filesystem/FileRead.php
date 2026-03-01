<?php
declare(strict_types=1);

/**
* Operations with file by serial read/write
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0b
* @created ?2009-03-25 13:51 ver 2.0b
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses file_base
* @uses VariableStream
**/

namespace Hubbitus\HuPHP\Filesystem;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;

class FileRead extends FileBase {
private $fd = null;

protected int $_line_no = 0; //Current line number. Read only. For getline() access.

	/**
	* If file opened before, content will be written in current position of file.
	* If it wasn't opened - open occurred.
	* @inheritdoc
	*
	* @param	int	Append by default if descriptor opened.	FILE_USE_INCLUDE_PATH supported if fd not opened en we open new.
	* @param	resource	$resource_context See {@link http://php.net/stream-context-create}.
	*	Used only if file opened here (was NOT opened before)
	* @return	int	Count of written bytes
	**/
	public function writeContent($flags = null, $resource_context = null): int {
		// If file was opened, write via file descriptor
		if ($this->fd) {
			// Truncate file to write from beginning
			ftruncate($this->fd, 0);
			rewind($this->fd);
			$result = fwrite($this->fd, $this->content);
			fflush($this->fd);

			if ($result === false) {
				throw new \RuntimeException('Failed to write content to file');
			}

			$this->_writePending = false;
			return (int)$result;
		}

		// Otherwise use direct file write
		$result = @file_put_contents($this->path(), $this->content, $flags ?? 0, $resource_context);

		if ($result === false) {
			throw new \RuntimeException('Failed to write content to file: ' . $this->path());
		}

		$this->_writePending = false;
		return (int)$result;
	}

	/**
	* Open file for reading/writing (according to $mode)
	*
	* @param	string	$mode. See {@link http://php.net/fopen}
	* @param	boolean	$use_include_path
	* @param	resource	$zContext  See {@link http://php.net/fopen}
	**/
	public function open(string $mode, bool $use_include_path = false, $zContext = null): void {
		$result = $zContext
			? ($this->fd = @\fopen($this->path(), $mode, $use_include_path, $zContext))
			: ($this->fd = @\fopen($this->path(), $mode, $use_include_path));

		// For write modes, just check if fopen succeeded
		if (\strpos($mode, 'w') !== false || \strpos($mode, 'a') !== false || \strpos($mode, 'x') !== false) {
			if ($result === false) {
				throw new \RuntimeException('Failed to open file for writing: ' . $this->path());
			}
		} else {
			// For read modes, use checkOpenError
			$this->checkOpenError((bool)$result);
		}

		$this->lineContent = [];
		$this->content = '';
	}

	/**
	* Get next line from stream.
	*
	* @param  int $length. Optional - maximum length of string. If null - all string returned (by default).
	* @return string|false
	* @throws VariableRequiredException
	**/
	public function getline(?int $length = null): string|false {
		++$this->_line_no;
		if ($length === 0 || $length < 0) return '';
		return $length ? \fgets(REQUIRED_VAR($this->fd), $length) : \fgets(REQUIRED_VAR($this->fd));
	}

	/**
	* Return current line number in getline() mode access.
	*
	* WARNING! Please keep in mind, it is not provide reliable interface to calculate real lines.
	* In current implementation by the fact it reflect count of invokes method ::getline() only!!!
	*
	* @return	int
	**/
	public function lineNo(): int {
		return $this->_line_no;
	}

	/**
	* Return tail of stream as string.
	*
	* {@link http://php.net/stream-get-contents}
	*
	* @param	int	$maxlength
	* @param	int	$offset
	* @return	string
	**/
	public function getTail (int $maxlength = -1, int $offset = 0): bool|string {
		return stream_get_contents($this->fd, $maxlength, $offset);
	}

	/**
	* Convert file content to string.
	*
	* @return	string
	**/
	public function __toString(): string {
		return $this->content ?? '';
	}
}
