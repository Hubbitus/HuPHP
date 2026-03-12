<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\Format\PrintoutDefault;
use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\System\OutputType;

/**
* Debug and backtrace toolkit.
* Backtrace implementation with Iterator interface.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 01:20 v 2.1b to 2.1.1
**/
class Backtrace implements \Iterator {
	/** @var array<mixed> */
	private array $_bt = [];

	private int $_curNode = 0;

	/** @var array<mixed> */
	protected array $_format = [];

	/**
	* Constructor
	*
	* @param array<mixed>|null $bt Array as result debug_backtrace() or it part. If null filled by direct debug_backtrace() call.
	* @param int $removeSelf If filled automatically, contains also this call (or call ::create() if appropriate). This will remove it. Number of arrays removed from stack.
	**/
	public function __construct(?array $bt = null, int $removeSelf = 1) {
		$this->_bt = $bt ?? \debug_backtrace();

		while ($removeSelf--) {
			\array_shift($this->_bt);
		}
	}

	/**
	* To allow constructions like: backtrace::create()->methodName()
	*
	* @param array<mixed>|null $bt {@link ::__construct}
	* @param int $removeSelf {@link ::__construct}
	* @return self
	**/
	public static function create(?array $bt = null, int $removeSelf = 2): self {
		return new self($bt, $removeSelf);
	}

	/**
	* Dump in appropriate(auto) form backtrace.
	* Fast dump of current backtrace may be invoked as backtrace::create()->dump();
	*
	* @deprecated since 2.1.5.1
	* @param bool $return
	* @param string $header
	* @return mixed return auto::a(...)
	**/
	public function dump(bool $return = false, string $header = '_debug_backtrace()'): mixed {
		return Dump::a($this->_bt, $header, $return);
	}

	/**
	* Get {@see BackTraceNode} by its number
	*
	* @param int|null $N Number of interested Node
	* @return BacktraceNode
	* @throws VariableRangeException
	**/
	public function getNode(?int $N): BacktraceNode {
		$N = $this->getNumberOfNode($N);
		if (isset($this->_bt[$N])) {
			if (\is_array($this->_bt[$N])) {
				// Cache on fly!!!
				$this->_bt[$N] = new BacktraceNode($this->_bt[$N], $N);
			}
			return $this->_bt[$N];
		} else {
			throw new VariableRangeException('Needed BackTraceNode not found in this BackTrace!');
		}
	}

	/**
	* Replace (or silently add) node in place $N
	*
	* @param int $N Place to node. If not exists - silently create.
	* @param BacktraceNode $node
	* @see ::getNumberOfNode() for more description
	**/
	public function setNode(int $N, BacktraceNode $node): void {
		$this->_bt[$this->getNumberOfNode($N)] = $node;
	}

	/**
	* Return real number of requested Node in _bt array, implements next logic:
	* If $N === null set on current node ({@see ::current()}).
	* If $N < 0 Negative values to do refer in backward: -2 mean: sizeof(debug_backtrace() - 2)!
	* Be careful value -1 meaning LAST element, not second from end!
	*
	* @param int|null $N
	* @return int Number of requested node.
	**/
	private function getNumberOfNode(?int $N): int {
		return ((null !== $N) ? ($N >= 0 ? $N : $this->length() + $N) : $this->key());
	}

	/**
	* Delete node in place $N
	* After delete, all indexes is recomputed. BUT, current position not changed!
	* So, be carefully in loops - it may have undefined behavior.
	*
	* @param int|null $N Place of node.
	* @return void
	* @throws VariableRangeException
	* @see ::getNumberOfNode() for more details
	**/
	public function delNode(?int $N = null): void {
		$calcN = $this->getNumberOfNode($N);
		if (!isset($this->_bt[$calcN])) {
			throw new VariableRangeException($N . ' node not found! Can\'t delete!');
		} else {
			// Do NOT use unset, because it left old keys
			\array_splice($this->_bt, $calcN, 1);
		}
	}

	/**
	* Return count of BackTraceNodes.
	*
	* @return int
	**/
	public function length(): int {
		return \sizeof($this->_bt);
	}

	/**
	* Find node of backtrace. To match each possible used fnmatch (http://php.net/fnmatch),
	* so all it patterns and syntax allowed.
	*
	* @param BacktraceNode $need Parameters to search:
	*  array(
	*      'file'      => "*backtrace.php"
	*      'class'     => "dump"
	*      'function'  => "[aw]"
	*      'type'      => "->"
	*  )
	* Array may contain next elements, each compared as *strings*: file, line, function, class,
	* object (yes it is, also compared as string, so it may have a sense if implemented __toString
	* magic method on it), type.
	* Args and N may be present, but first is stupidly compare as string ('Array' === 'Array' :))
	* and to search by N use ::getNode() this faster.
	* @return Backtrace
	**/
	public function find(BacktraceNode $need): Backtrace {
		$ret = clone $this;

		// Foreach is dangerous, because we delete elements.
		$ret->rewind();
		while ($node = $ret->current()) {
			// Returned 0 if equals
			if ($node->fnmatchCmp($need) !== 0) {
				$ret->delNode();
			} else {
				$node = $ret->next();
			}
		}
		return $ret;
	}

	/**
	* @todo Implement RegExp find. Not now.
	*
	* @param BacktraceNode $need
	* @return void
	* @throws BaseException
	**/
	public function findRegexp(BacktraceNode $need): void {
		throw new BaseException('Method findRegexp not implemented now!');
	}

	/**
	* Adopted from http://php.rinet.ru/manual/ru/function.debug-backtrace.php
	* comments of users
	*
	* @param array<mixed>|null $format If null, trying from format set in {@see ::setPrintoutFormat()}, and finally get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param OutputType|null $outType If present - determine type of format from $format (passed or default). Must be index in $format.
	* @return string
	**/
	public function printFormat(?array $format = null, ?OutputType $outType = null): string {
		$outType ??= OS::getOutType();

		// Ensure default format is configured
		if (!isset($GLOBALS['__CONFIG']['backtrace::printout']) || $GLOBALS['__CONFIG']['backtrace::printout'] === []) {
			PrintoutDefault::configure();
		}

		// Get format from parameter, or from instance format, or from global config
		$format ??= $this->_format[$outType->name]
			?? $GLOBALS['__CONFIG']['backtrace::printout'][$outType->name]
			?? $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name];

		$hf = new HuFormat($format, $this);
		return $hf->getString();
	}

	/**
	* Set format to printout. Array by type of out as key {@see OS::OUT_* constants}, and values as array in format,
	* as described in {@see class HuFormat}. {@example Debug/_HuFormat.defaults/backtrace::printout.php}
	* On time of set format NOT CHECKED!
	*
	* @param array<mixed> $format
	* @return $this
	* @throws VariableRequiredException
	**/
	public function &setPrintoutFormat(array $format): static {
		$this->_format = Vars::requiredNotEmpty($format);
		return $this;
	}

	/**
	* By default convert into string will ::printFormat();
	*
	* @return string
	**/
	public function __toString(): string {
		return $this->printFormat(null);
	}

	/*
	* From interface Iterator
	* Use self indexing to allow delete nodes and continue loop foreach.
	*/

	/**
	* Rewind internal pointer to begin
	*
	* @return void
	**/
	public function rewind(): void {
		$this->_curNode = 0;
	}

	/**
	* Return current backtraceNode
	*
	* @return BacktraceNode|null
	**/
	public function current(): mixed {
		try {
			return $this->getNode($this->_curNode);
		} catch (VariableRangeException $vre) {
			return null;
		}
	}

	/**
	* Return current key
	*
	* @return int
	**/
	public function key(): mixed {
		return $this->_curNode;
	}

	/**
	* Return next backtraceNode
	*
	* @return BacktraceNode|null
	**/
	#[\ReturnTypeWillChange]
	public function next(): BacktraceNode|null {
		try {
			return $this->getNode(++$this->_curNode);
		} catch (VariableRangeException $vre) {
			return null;
		}
	}

	/**
	* Return if Iterator valid and not end reached.
	*
	* @return bool
	**/
	public function valid(): bool {
		return ($this->current() !== null);
	}

	/**
	* Return end backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	* and added to more flexibility.
	*
	* @return BacktraceNode
	**/
	public function end(): BacktraceNode {
		return $this->getNode(($this->_curNode = $this->length() - 1));
	}

	/**
	* Return prev backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	* and added to more flexibility.
	*
	* @return BacktraceNode|null
	**/
	public function prev(): ?BacktraceNode {
		if ($this->_curNode < 1) {
			return null;
		}
		return $this->getNode(--$this->_curNode);
	}
}
