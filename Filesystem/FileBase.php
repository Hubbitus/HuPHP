<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotExistsException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotReadableException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\System\OS;

/**
* Base class for most file-related operations.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created ?2008-08-27 ver 1.0 to 1.1
**/
class FileBase {
	private string $filename = '';
	private string $rawFilename = ''; //Filename to try open. For error-reports.
	protected $_writePending = false;

	/** Pending content for write **/
	protected	$content;

	/**
	* Construct new object with provided (optional) path (URL).
	*
	* @param string $filename
	**/
	public function __construct($filename = '') {
		if ($filename !== '') {
			$this->setPath($filename);
		}
	}

	/**
	* Write all pending write if it wasn't be done manually before. This is to avoid data loss.
	**/
	public function __destruct() {
		if ($this->_writePending) {
			$this->writeContent();
		}
	}

	/**
	* Set new path. For example to writing new file.
	*
	* @param string $filename New filename
	* @return static
	**/
	public function &setPath($filename): static {
		$this->filename = $this->rawFilename = $filename;
		/**
		* And we MUST set full path in ->filename because after f.e. chdir(...) relative path may change sense.
		* Additionally, in __destruct call to getcwd return '/'!!! {@See http://bugs.php.net/bug.php?id=30210}
		**/
		// We can't direct use $this->filename instead of $realpath because if it ! we not always want null it!
		if (!($realpath = \realpath($this->rawFilename))) {
			/**
			* Realpath may fail because file not found. But we can't agree with that,
			* because setPath may be invoked to set path for write new (create) file!
			* So, we try manually construct current full path (see above why we should do it)
			**/
			if (!OS::isPathAbsolute($this->rawFilename)) {
				$this->filename = \getcwd() . DIRECTORY_SEPARATOR . $this->rawFilename;
			}
		} else {
			$this->filename = $realpath;
		}
		return $this;
	}

	/**
	* Return current path
	*
	* @return	string
	**/
	public function path(): string {
		return $this->filename;
	}

	/**
	* Return current RAW (what which be passed into the {@see setPath()}, without any transformation) path.
	*
	* @return	string
	**/
	public function rawPath(): string {
		return $this->rawFilename;
	}

	/**
	* Return true if current set path is exists.
	*
	* @return boolean
	**/
	public function isExists(): bool {
		// Very strange: file_exists('') === true!!!
		return ('' !== $this->path() and \file_exists($this->path()));
	}

	/**
	* Return true, if file on current path is readable.
	*
	* @return	boolean
	**/
	public function isReadable(): bool {
		return \is_readable($this->path());
	}

	/**
	* Unlink (delete) file
	*
	* @return boolean
	**/
	public function unlink(): bool {
		return \unlink($this->path());
	}

	/**
	* Return directory part of current path (file must not be exist!).
	*
	* @return	string
	**/
	public function getDir(): string {
		return \dirname($this->path());
	}

	/**
	* Clear pending writes.
	*
	* @return static
	**/
	public function &clearPendingWrite(): static {
		$this->_writePending = false;
		return $this;
	}

	/**
	* Set content for write.
	*
	* @param string	$string. String to set from.
	* @return static
	* @throws VariableRequiredException
	**/
	public function &setContentFromString($string): static {
		$this->content = Vars::requiredNotNull($string);
		$this->_writePending = true;
		return $this;
	}

	/**
	* Append string to pending write buffer.
	*
	* @param	string	$string. String to append from.
	* @return static
	* @throws VariableRequiredException
	**/
	public function &appendString($string): static {
		$this->content .= Vars::requiredNotEmpty($string);
		$this->_writePending = true;
		return $this;
	}

	/**
	* Write whole content to file (filename may be set via ->setPath('NewFileName'))
	*
	* @param ?int $flags See {@link http://php.net/file_put_contents}
	* @param resource|null $resource_context See {@link http://php.net/stream-context-create}
	* @return int Count of written bytes
	**/
	public function writeContent($flags = 0, $resource_context = null): int {
		try {
			$this->checkOpenError(
				false !== ($count = @\file_put_contents($this->path(), $this->content, $flags ?? 0, $resource_context))
			);
		} finally {
			$this->_writePending = false;
		}

		return $count;
	}

	protected function checkOpenError($succ): void {
		if (!$succ) {
			if (!$this->isExists()) {
				throw new FileNotExistsException('File not found', $this->path());
			}
			if (!$this->isReadable()) {
				throw new FileNotReadableException('File not readable. Check permissions.', $this->path());
			}
			throw new FileNotReadableException('Unknown error operate on file.', $this->path());
		}
	}
}
