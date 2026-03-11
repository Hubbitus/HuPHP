<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Filesystem;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Macro\Vars;

/**
* Operations with file by serial read/write
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-25 13:51 ver 2.0b
**/
class FileRead extends FileBase {
	/** @var resource|null */
	private $fd = null;

	protected int $_line_no = 0; //Current line number. Read only. For getline() access.

	/**
	* If file opened before, content will be written in current position of file.
	* If it wasn't opened - open occurred.
	* @inheritdoc
	*
	* @param int|null $flags Append by default if descriptor opened. FILE_USE_INCLUDE_PATH supported if fd not opened en we open new.
	* @param resource|null $resource_context See {@link http://php.net/stream-context-create}.
	*   Used only if file opened here (was NOT opened before)
	* @return int Count of written bytes
	**/
	public function writeContent($flags = null, $resource_context = null): int {
		// If file was opened, write via file descriptor
		if ($this->fd !== null) {
			// Truncate file to write from beginning
			\ftruncate($this->fd, 0);
			\rewind($this->fd);
			// Suppress warning - fwrite fails with bad file descriptor when fd is read-only
			$result = @\fwrite($this->fd, $this->content);
			\fflush($this->fd);

			if ($result === false) {
				throw new \RuntimeException('Failed to write content to file');
			}

			$this->_writePending = false;
			return $result;
		}

		// Otherwise use direct file write
		$result = @\file_put_contents($this->path(), $this->content, $flags ?? 0, $resource_context);

		if ($result === false) {
			throw new \RuntimeException('Failed to write content to file: ' . $this->path());
		}

		$this->_writePending = false;
		return $result;
	}

	/**
	* Open file for reading/writing (according to $mode)
	*
	* @param string $mode See {@link http://php.net/fopen}
	* @param bool $use_include_path
	* @param resource $resource_context  See {@link http://php.net/fopen}
	**/
	public function open(string $mode, bool $use_include_path = false, $resource_context = null): void {
		if ($resource_context !== null) {
			$this->fd = @\fopen($this->path(), $mode, $use_include_path, $resource_context);
		} else {
			$this->fd = @\fopen($this->path(), $mode, $use_include_path);
		}

		// For write modes, just check if fopen succeeded
		if (\strpos($mode, 'w') !== false || \strpos($mode, 'a') !== false || \strpos($mode, 'x') !== false) {
			if ($this->fd === false) {
				throw new \RuntimeException('Failed to open file for writing: ' . $this->path());
			}
		} else {
			// For read modes, use checkOpenError
			$this->checkOpenError((bool)$this->fd);
		}

		$this->content = '';
	}

	/**
	* Get next line from stream.
	*
	* @param  int $length Optional - maximum length of string. If null - all string returned (by default).
	* @return string|false
	* @throws VariableRequiredException
	**/
	public function getline(?int $length = null): string|false {
		++$this->_line_no;
		if ($length === 0 || $length < 0) {
			return '';
		}
		return $length !== null ? \fgets(Vars::requiredNotEmpty($this->fd), $length) : \fgets(Vars::requiredNotEmpty($this->fd));
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
	* @param int $maxlength
	* @param int $offset
	* @return string|false
	**/
	public function getTail (int $maxlength = -1, int $offset = 0): string|false {
		// Check if fd is readable before attempting to read
		if ($this->fd !== null && \is_resource($this->fd)) {
			$meta = \stream_get_meta_data($this->fd);
			if ($meta['mode'] === 'w') {
				// File opened for writing only - cannot read
				throw new \RuntimeException('Cannot read from file opened in write mode');
			}
		}
		// Suppress warning - stream_get_contents fails with bad file descriptor when fd is write-only
		return @\stream_get_contents($this->fd, $maxlength, $offset);
	}

	/**
	* Convert file content to string.
	*
	* @return string
	**/
	public function __toString(): string {
		return $this->content ?? '';
	}

	/**
	* Destructor - only write if file was opened for writing.
	* Override parent to avoid writing to read-only file descriptors.
	**/
	public function __destruct() {
		// Don't write if file descriptor is open for reading
		// Check mode before attempting write
		if ($this->fd !== null && \is_resource($this->fd)) {
			$meta = \stream_get_meta_data($this->fd);
			if ($meta['mode'] === 'r') {
				// File opened for reading only - skip write
				return;
			}
		}
		// Safe to write - call parent
		parent::__destruct();
	}
}
