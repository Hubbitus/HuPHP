<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use function Hubbitus\HuPHP\macroses\REQUIRED_VAR;
use Hubbitus\HuPHP\Debug\HuFormat;
use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\Debug\Format\PrintoutDefault;

/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage Backtrace
* @version 2.1.6
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 01:20 v 2.1b to 2.1.1
*
* @uses ASSIGN_IF()
* @uses EMPTY_VAR()
* @uses REQUIRED_VAR()
* @uses VariableEmptyException
* @uses VariableArrayInconsistentException
* @uses VariableRangeException
* @uses VariableRequiredException
* @uses BacktraceEmptyException
* @uses ClassPropertyNotExistsException
* @uses HuFormat
**/

class Backtrace implements \Iterator {
	private array $_bt = [];

	private int $_curNode = 0;
	protected array $_format = [];

	/**
	* Constructor
	*
	* @param	array	$bt	Array as result debug_backtrace() or it part. If null filled by
	*	direct debug_backtrace() call.
	* @param	int(1)	$removeSelf	If filled automatically, contains also this call
	*	(or call ::create() if appropriate). This will remove it. Number is amount of arrays
	*	remove from stack.
	* @return	Backtrace
	**/
	public function __construct(?array $bt = null, int $removeSelf = 1){
		if ($bt) $this->_bt = $bt;
		else $this->_bt = \debug_backtrace();

		while ($removeSelf--) \array_shift($this->_bt);
	}
	/**
	* To allow constructions like: backtrace::create()->methodName()
	*
	* @param	array	$bt	{@link ::__construct}
	* @param	int(2)	$removeSelf	{@link ::__construct}
	* @return	Backtrace
	**/
	public static function create(?array $bt = null, $removeSelf = 2){
		return new self($bt, $removeSelf);
	}
	/**
	* Dump in appropriate(auto) form backtrace.
	*	Fast dump of current backtrace may be invoked as backtrace::create()->dump();
	*
	* @deprecated since 2.1.5.1
	* @param	boolean	$return
	* @param	string	$header('_debug_backtrace()')
	* @return	mixed	return auto::a(...)
	**/
	public function dump($return = false, $header = '_debug_backtrace()'){
		return Dump::a($this->_bt, $header, $return);
	}
	/**
	* Get BackTraceNode by its number
	*
	* @param	integer	$N - Number of interested Node
	* @return	BacktraceNode
	* @throws VariableRangeException
	**/
	public function getNode($N){
		if (isset($this->_bt[ $N = $this->getNumberOfNode($N) ])){
			if (is_array($this->_bt[ $N ])){
			//Cache on fly!!!
			$this->_bt[ $N ] = new BacktraceNode($this->_bt[$N], $N);
			}
		//instanceof backtraceNode
		return $this->_bt[$N];
		}
		else throw new VariableRangeException('Needed BackTraceNode not found in this BackTrace!');
	}
	/**
	* Replace (or silently add) node in place $N
	*
	* @param	integer	$N	Place to node. If not exists - silently create.
	*	{@see ::getNumberOfNode() for more description}
	* @return	void
	**/
	public function setNode($N, BacktraceNode $node): void {
		$this->_bt[ $this->getNumberOfNode($N) ] = $node;
	}
	/**
	* Return real number of requested Node in _bt array, implements next logic:
	*	If $N === null set on current node ({@see ::current()}).
	*	If $N < 0	Negative values to to refer in backward: -2 mean: sizeof(debug_backtrace() - 2)!
	*		Be careful value -1 meaning LAST element, not second from end!
	*
	* @param	integer	$N
	* @return	integer	Number of requested node.
	**/
	private function getNumberOfNode($N): int {
		return ( (null !== $N) ? ($N >= 0 ? $N : $this->length() + $N) : $this->key() );
	}
	/**
	* Delete node in place $N
	* After delete, all indexes is recomputed. BUT, current position not changed!
	* So, be carefully in loops - it may have undefined behavior.
	*
	* @param	integer	$N	Place of node.
	*	{@see ::getNumberOfNode() for more details}
	* @return	void
	* @throws VariableRangeException
	**/
	public function delNode($N = null): void {
		if (!isset($this->_bt[ $calcN = $this->getNumberOfNode($N)])){
			throw new VariableRangeException($N.' node not found! Can\'t delete!');
		}
		else{
			//Do NOT use unset, because it left old keys
			array_splice($this->_bt, $calcN, 1);
		}
	}
	/**
	* Return count of BackTraceNodes.
	*
	* @return	integer
	**/
	public function length(){
		return sizeof($this->_bt);
	}
	/**
	* Find node of backtrace. To match each possible used fnmatch (http://php.net/fnmatch),
	* so all it patterns and syntax allowed.
	*
	* @param	BacktraceNode	$need	Parameters to search:
	* 	array(
	*		'file'	=> "*backtrace.php"
	*		'class'	=> "dump"
	*		'function'=> "[aw]"
	*		'type'	=> "->"
	*	)
	* Array may contain next elements, each compared as *strings*: file, line, function, class,
	* object (yes it is, also compared as string, so it may have a sense if implemented __toString
	* magic method on it), type.
	*	Args and N may be present, but first is stupidly compare as string ('Array' === 'Array' :))
	* and to search by N use ::getNode() this faster.
	* @return	Backtrace
	**/
	public function find(BacktraceNode $need){
		$ret = clone $this;

		//Foreach is dangerous, because we delete elements.
		$ret->rewind();
		while ($node = $ret->current()){
			//Returned 0 if equals
			if ($node->fnmatchCmp($need) != 0){
				$ret->delNode();
			}
			else{
				$node = $ret->next();
			}
		}
		return $ret;
	}
	/**
	* @todo Implement RegExp find. Not now.
	**/
	public function findRegexp(BacktraceNode $need){
		throw new BaseException('Method findRegexp not implemented now!');
	}

	/**
	* Adopted from http://php.rinet.ru/manual/ru/function.debug-backtrace.php
	* comments of users
	*
	* @param	boolean(false)	$return	Return or print directly.
	* @param	array(null)	$format
	*	If null, trying from format set in {@see ::setPrintoutFormat()}, and finally
	*		get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param	integer(null)	$OutType	If present - determine type of format from $format (passed or default). Must be index in $format.
	* @Throws(VariableRequiredException, BacktraceEmptyException)
	**/
	public function printFormat(?array $format = null, ?int $outType = null): mixed {
		$outType ??= OS::getOutType();

		// Ensure default format is configured
		if (empty($GLOBALS['__CONFIG']['backtrace::printout'])) {
			PrintoutDefault::configure();
		}

		// Get format from parameter, or from instance format, or from global config
		$format ??= $this->_format[$outType]
			?? $GLOBALS['__CONFIG']['backtrace::printout'][$outType]
			?? $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']
			?? null;

		if ($format === null) {
			throw new VariableRequiredException(new Backtrace(), 'format', 'No format provided for printout');
		}

		if ($this->_bt) {
			$hf = new HuFormat($format, $this);
			$ret = $hf->getString();
		}
		else {
			// Return empty string for empty backtrace instead of throwing exception
			// This allows graceful handling in contexts like OutExtraDataBacktrace
			$ret = '';
		}

		return $ret;
	}

	/**
	* Set format to printout. Array by type of out as key {@see OS::OUT_* constants}, and values as array in format,
	*	as described in {@see class HuFormat}. {@example Debug/_HuFormat.defaults/backtrace::printout.php}
	*	On time of set format NOT CHECKED!
	*
	* @param	array	$format
	* @return	&$this
	* @Throws(VariableRequiredException)
	**/
	public function &setPrintoutFormat($format){
		$this->_format = REQUIRED_VAR($format);
		return $this;
	}

	/**
	* By default convert into string will ::printout();
	*
	* @return string
	**/
	public function __toString(){
		return $this->printFormat(true);
	}

	/*
	* From interface Iterator
	* Use self indexing to allow delete nodes and continue loop foreach.
	**/

	/**
	* Rewind internal pointer to begin
	**/
	public function rewind(): void{
		$this->_curNode = 0;
	}

	/**
	* Return current backtraceNode
	*
	* @return	BacktraceNode|null
	**/
	public function current(): mixed {
		try{
			return $this->getNode($this->_curNode);
		}
		catch (VariableRangeException $vre){
			return null;
		}
	}

	/**
	* Return current key
	*
	* @return	integer
	**/
	public function key(): mixed {
		return $this->_curNode;
	}

	/**
	* Return next backtraceNode
	*
	* @return	BacktraceNode|null
	**/
	#[\ReturnTypeWillChange]
	public function next(): BacktraceNode|null {
		try{
			return $this->getNode( ++$this->_curNode );
		}
		catch (VariableRangeException $vre){
			return null;
		}
	}

	/**
	* Return if Iterator valid and not end reached.
	*
	* @return	bool
	**/
	public function valid(): bool {
		return ($this->current() !== null);
	}

	/**
	* Return end backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	*
	* @return	BacktraceNode
	**/
	public function end(){
		return $this->getNode( ($this->_curNode = $this->length() - 1) );
	}

	/**
	* Return prev backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	*
	* @return	BacktraceNode|null
	**/
	public function prev(){
		if ($this->_curNode < 1) return null;

		return $this->getNode( --$this->_curNode );
	}
}
