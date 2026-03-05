<?php
declare(strict_types=1);

/**
* Operations with file in memory.
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0.1b
* @created ?2009-03-25 13:51 ver 2.0b
*
* @uses VariableRequiredException
* @uses file_base
**/

namespace Hubbitus\HuPHP\Filesystem;

use function Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException;
use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\System\Process;

class FileInMemory extends FileBase {
private array $lineContent = [];

private string $_lineSep = "\n"; // Unix by default

private array $_linesOffsets = []; // Cache For ->getLineByOffset and ->getOffsetByLine methods

	/**
	* Load full content of file into memory.
	*
	* If file very big consider read it for example by lines, if task allow it.
	*
	* @param	boolean	$use_include_path
	* @param	resource	$resource_context
	* @param	integer	$offset
	* @param	integer	$maxLen
	* @return static
	**/
	public function &loadContent(bool $use_include_path = false, $resource_context = null, ?int $offset = null, ?int $maxLen = null): static {
		$this->checkOpenError(
			false !== (
				($maxLen !== null && $offset !== null)
					? ($this->content = file_get_contents($this->path(), $use_include_path, $resource_context, $offset, $maxLen))
					: (($offset !== null)
						? ($this->content = file_get_contents($this->path(), $use_include_path, $resource_context, $offset))
						: ($this->content = file_get_contents($this->path(), $use_include_path, $resource_context))
					)
			)
		);
		$this->lineContent = [];
		$this->_linesOffsets = [];
		return $this;
	}

	/**
	* @inheritdoc
	**/
	public function &setContentFromString($string): static {
		$this->lineContent = [];
		$this->_linesOffsets = [];
		return parent::setContentFromString(REQUIRED_NOT_NULL($string));
	}

	/**
	* Partial write not supported, reset full string to resplit by lines it in future.
	* @inheritdoc
	**/
	public function &appendString($string): static {
		return $this->setContentFromString($this->content . REQUIRED_VAR($string));
	}

	/**
	* @inheritdoc
	*
	* Additional parameters are:
	* @param string|null $implodeWith See {@see ::implodeLines()}
	* @param bool $updateLineSep See {@see ::implodeLines()}
	**/
	public function writeContent($flags = null, $resource_context = null, ?string $implodeWith = null, bool $updateLineSep = true): int {
		try {
			$this->checkOpenError(
				// $this->rawFilename because may be file generally not exists!
				false !==  ($count = @file_put_contents($this->path(), $this->getBLOB($implodeWith, $updateLineSep), $flags ?? 0, $resource_context))
			);
		} finally {
			$this->_writePending = false;
		}
		return $count;
	}

	/**
	* Return array of specified lines or all by default
	*
	* @param array $lines If empty array - whole array of lines. Else
	*   Array(int $offset [, int $length [, bool $preserve_keys ]]). See http://php.net/array_slice
	* @param bool $updateLineSep See explanation in ->explodeLines() method.
	* @return array Array of lines
	**/
	public function getLines(array $lines = [], bool $updateLineSep = true): array {
		$this->checkLoad();
		if ($this->lineContent === []) {
			$this->explodeLines($updateLineSep);
		}

		if ($lines !== []) {
			$offset = (int) ($lines[0] ?? 0);
			$length = isset($lines[1]) ? (int) $lines[1] : null;
			return \array_slice($this->lineContent, $offset, $length);
		} else {
			return $this->lineContent;
		}
	}

	/**
	* Explode loaded content to lines.
	*
	* @param bool $updateLineSep if true - update lineSep by presented in whole content.
	**/
	protected function explodeLines(bool $updateLineSep = true): void {
		if ($this->content === null || $this->content === '') {
			$this->lineContent = [];
			$this->_linesOffsets = [];
			return;
		}
		\preg_match_all('/(.*?)([\n\r]|\z)/', $this->content, $matches, PREG_PATTERN_ORDER);
		if ($updateLineSep && isset($matches[2][0])) {
			$this->_lineSep = $matches[2][0];
		}
		// Remove last empty element if present
		$lines = $matches[1];
		if (\count($lines) > 0 && \end($lines) === '') {
			\array_pop($lines);
		}
		$this->lineContent = $lines;
		$this->_linesOffsets = [];
	}

	/**
	* Implode lineContent to whole contents.
	*
	* @param string $implodeWith String implode with. If null, by default - ->_lineSep.
	* @param bool $updateLineSep if true - update lineSep by presented $implodeWith.
	**/
	protected function implodeLines(?string $implodeWith = null, bool $updateLineSep = true): string {
		if ($implodeWith !== null && $updateLineSep) {
			$this->setLineSep($implodeWith);
		}
		$this->_linesOffsets = [];
		return ($this->content = implode($implodeWith ?? $this->_lineSep, $this->lineContent));
	}

	/**
	* Return string of content
	*
	* @param string $implodeWith See {@see ::implodeLines()}
	* @param bool $updateLineSep See {@see ::implodeLines()}
	* @return string
	**/
	public function getBLOB(?string $implodeWith = null, bool $updateLineSep = true): string {
		if (
			! $this->content
			or
			($implodeWith !== null && $implodeWith !== $this->_lineSep)
		) {
			$this->implodeLines($implodeWith, $updateLineSep);
		}
		return $this->content;
	}

	/**
	* Set new line separator.
	*
	* It also may be used to convert line separators like:
	* $f = new file_inmem('filename');
	* $f->setLineSep("\r\n")->loadContent()->setLineSep("\n")->writeContent();
	*	or even more easy:
	* $f->setLineSep("\r\n")->loadContent()->->writeContent(nul, null, "\n");
	*
	* @param string $newSep
	* @return static
	**/
	public function &setLineSep(string $newSep): static {
		$this->_lineSep = $newSep;
		$this->_linesOffsets = [];
		return $this;
	}

	/**
	* Get current line separator.
	*
	* @return string
	**/
	public function getLineSep(): string {
		return $this->_lineSep;
	}

	/**
	* Return line with requested number.
	*
	* Boundaries NOT checked!
	*
	* @param int $line
	* @return string|null
	**/
	public function getLineAt(int $line, bool $updateLineSep = true): mixed {
		if ([] === $this->lineContent) {
			if ($this->content === null || $this->content === '') {
				$this->loadContent();
			}
			$this->explodeLines($updateLineSep);
		}
		return $this->lineContent[$line] ?? null;
	}

	/**
	* Calculate line number by file offset.
	*
	* @param int $offset
	* @return int|false
	* @throws VariableRangeException
	**/
	public function getLineByOffset(int $offset): int|false {
		if ([] === $this->_linesOffsets) {
			$this->makeCacheLineOffsets();
		}
		if ($offset > $this->_linesOffsets[\count($this->_linesOffsets)-1][1]) {
			throw new VariableRangeException("Overflow! Offset [$offset] does not exists in [{$this->path()}].");
		}

		// Data ordered - provide binary search as fast alternative to array_search
		$size = \count($this->_linesOffsets) - 1; // For speed up only
		$left = 0;
		$right = $size; // Points of interval
		$found = false;
		$line = (int)\ceil($size / 2);

		/*
		* Boundary conditions. Additional check of lowest value is mandatory, if used ceil() (0 is not accessible).
		* Additional check of highest value added only to efficient adjusting, because on it point the maximum time for the
		* convergence of the algorithm
		**/
		if ($offset >= $this->_linesOffsets[0][0] && $offset <= $this->_linesOffsets[0][1]) {
			return 0;
		}

		if ($offset >= $this->_linesOffsets[$size][0] && $offset <= $this->_linesOffsets[$size][1]) {
			return $size;
		}

		do {
			if ($offset >= $this->_linesOffsets[$line][0]) {
				if ($offset <= $this->_linesOffsets[$line][1]) {
					$found = true; // done
				} else {
					$left = $line;
					$line += (int)\ceil( ($right - $line) / 2 );
				}
			} else {
				$right = $line;
				$line -= (int)\ceil( ($line - $left) / 2);
			}
		} while (!$found);

		return $line;
	}

	/**
	* Opposite to {@see ::getLineByOffset()} return offset of line begin.
	*
	* @param int $line
	* @return array{int, int}
	* @throws VariableRangeException
	**/
	public function getOffsetByLine(int $line): array {
		if ($this->_linesOffsets === []) {
			$this->makeCacheLineOffsets();
		}
		if ($line >= \count($this->_linesOffsets)) {
			throw new VariableRangeException("Overflow! Line [$line] does not exists in [{$this->path()}].");
		}

		return $this->_linesOffsets[$line];
	}

	/**
	* Check loaded content is not empty. Throw exception otherwise.
	*
	* @return static
	* @throws VariableEmptyException
	**/
	private function &checkLoad(): static {
		if ($this->lineContent === [] && ($this->content === '' || $this->content === null)) {
			throw new VariableEmptyException(new Backtrace(), 'Line-Content and Content is empty! May be you forgot call one of ->load*() method first?');
		}
		return $this;
	}

	/**
	* Make cache of lines and its offsets.
	**/
	private function makeCacheLineOffsets(): void {
		$this->_linesOffsets = [];
		$offset = 0;
		$lines = $this->getLines();

		$linesCount = \count($lines);
		// First line is additional case
		$this->_linesOffsets[0] = [$offset, ($offset += -1 + \strlen(\mb_convert_encoding($lines[0], 'ISO-8859-1', 'UTF-8')) + \strlen(\mb_convert_encoding($this->getLineSep(), 'ISO-8859-1', 'UTF-8')))];
		// From 1 line, NOT 0
		for ($i = 1; $i < $linesCount; $i++) {
			$this->_linesOffsets[$i] = [
				$offset + 1,
				( $offset += \strlen(\mb_convert_encoding($lines[$i], 'ISO-8859-1', 'UTF-8')) + \strlen(\mb_convert_encoding($this->getLineSep(), 'ISO-8859-1', 'UTF-8')) )
			];
		}
	}

	/**
	* Iconv content from one charset to another. If in charset is not known consider use method {@see ::enconv()}
	*
	* @uses iconv
	* @param string $fromEnc
	* @param string $toEnc
	* @return static
	**/
	public function &iconv(string $fromEnc, string $toEnc = 'UTF-8'): static {
		$this->setContentFromString(iconv($fromEnc, $toEnc, $this->getBLOB()));
		return $this;
	}

	/**
	* Uses shell execute enconv to guess encoding and convert it to desired
	*
	* @uses Process
	* @uses shell enconv
	* @param string $lang
	* @param string $toEnc
	* @return static
	**/
	public function &enconv(string $lang = 'russian', string $toEnc = 'UTF-8'): static {
		$this->setContentFromString(Process::exec("enconv -L $lang -x $toEnc", null, null, $this->getBLOB()));
		return $this;
	}

	/**
	* Get content length in bytes.
	*
	* @return	int
	**/
	public function getContentLength(): int {
		return \strlen($this->getBLOB());
	}

	/**
	* Get first line of content.
	*
	* @return	string|null
	**/
	public function getFirstLine(): ?string {
		$lines = $this->getLines();
		return $lines[0] ?? null;
	}

	/**
	* Get last line of content.
	*
	* @return	string|null
	**/
	public function getLastLine(): ?string {
		$lines = $this->getLines();
		return $lines[\count($lines) - 1] ?? null;
	}
}
