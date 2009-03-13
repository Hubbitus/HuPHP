<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage Bactrace
* @version 2.1.6
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-30 01:20 v 2.1b to 2.1.1
*	- Move Include debug.php to method ::dump, where only it may be used.
*
*	* 2008-05-30 14:19  v 2.1.1 to 2.1.2
*	- Add capability to PHP < 5.3.0-dev:
*		* Replace construction ($var ?: "text") to ($var ? '' : "text")
*		* Around "new static" (which is more "correct") in eval. Oterwise php scream what it is not known "static" and get parse error!
*		 return eval('return new static($arr, $N);');
*
*	* 2008-08-27 20:07 v 2.1.2 to 2.1.3
*	- Modify include and check conditions in formatArgs() and printout() methods
*
*	* 2008-09-07 22:02 v 2.1.3 to 2.1.4
*	- In methods printout() and formatArgs() add cache of $OutType. Fix errors in them with inclusion (see comment below.).
*
*	* 2008-09-14 21:48 v 2.1.4 to 2.1.5
*	- Add class-exception BacktraceEmpty
*	- Add check to non-empty backtrace before formatting it in printout method. Now it may throw BacktraceEmptyException  
*
*	* 2008-09-15 17:34 v 2.1.5 to 2.1.5.1
*	- Delete some excessive debug comments.
*
*	* 2009-03-08 13:24 ver 2.1.5.1 to 2.1.6
*	- Reformat huge PhpDocs
*	- Method setPrintoutFormat now return &$this
*	- Add and implement __toString() method through ::printout()
**/

include_once('macroses/ASSIGN_IF.php');
include_once('macroses/EMPTY_VAR.php');
include_once('macroses/REQUIRED_VAR.php');
include_once('Exceptions/classes.php');
include_once('Exceptions/variables.php');
include_once('Debug/HuFormat.php');

class BacktraceEmptyException extends VariableEmptyException{}

/**
* BackTraceNode. In array converted to like this. Otherwise each member accessible separately.
* Structure example:
* Array(){
*	[file] => string(37) "/var/www/_SHARED_/Debug/backtrace.php"	//Mandatory
*	[line] => int(47)	//Mandatory
*	[function] => string(11) "__construct"	//Mandatory
*	[class] => string(9) "backtrace"
*	[object] => object(backtrace)#1 (2) { <Full Object> }
*	[type] => string(2) "->"
*	[args] => Array(2){	//Mandatory
*		[0] => NULL
*		[1] => int(0)
*	}
* 	//Additional according to standad element of array from debug_backtrace();
*	//Point to number in element of array debug_backtrace();
* 	[N] => 1	//Mandatory
* }
*
* implements Iterator by example from main descrioption http://php.net/manual/ru/language.oop5.iterations.php
**/
class backtraceNode implements Iterator{
static public $properties = array(
	'file',
	'line',
	'function',
	'class',
	'object',
	'type',
	'args',
	'N'
);

private $_btn = null;

protected $_format;	/** Format to format args to string {@see setArgsFormat} **/

	/**
	* Construct object from array
	*
	* @param	array	$arr	Array to construct from
	* @param	$N		Number of node, got separatly (may be already in $arr).
	* @return	Object(backtraceNode)
	**/
	public function __construct(array $arr = null, $N = false){
	ASSIGN_IF($this->_btn, $arr);
		if (false !== $N) $this->_btn['N'] = $N;
	}#m __construct

	/**
	* To allow constructions like: backtraceNode::create()->methodName()
	* {@inheritdoc ::__construct()}
	**/
	static public function create(array $arr = null, $N = false){
		/*
		* Require late-static-bindings future, so, it is available only in PHP version >= 5.3.0-dev
		**/
		if (version_compare(PHP_VERSION, '5.3.0-dev', '>=')){
		return eval('return new static($arr, $N);');
		}
		else{//This is legitimate onli if it has not derived. So, now it is true...
		return new self($arr, $N);
		}
	}#m create

	/**
	* Return property, if it exists, Throw ClassPropertyNotExistsException otherwise
	*
	* @param	string	$name	Name of required property
	* @return	mixed	Reference on property value
	* @Throw(ClassPropertyNotExistsException)
	**/
	public function &__get($name){
		if (!in_array($name, backtraceNode::$properties)) throw new ClassPropertyNotExistsException('Property "'.$name.'" does NOT exist!');

	return $this->_btn[$name];
	}#m __get

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of required property
	* @return	boolean
	**/
	public function __isset($name) {
		if (!in_array($name, backtraceNode::$properties)) throw new ClassPropertyNotExistsException('Property <'.$name.'> does NOT exist!');

	return isset($this->_btn[$name]);
	}#m __isset

	/**
	* Dump in appropriate(auto) form bactraceNode.
	*
	* @param	boolean	$return
	* @param	string	$header('backtraceNode')
	* @return	mixed	return dump::a(...)
	**/
	public function dump($return = false, $header = 'backtraceNode'){
	return dump::a($this->_btn, $header, $return);
	}#m dump

/**#########################################################
##From interface Iterator
##########################################################*/

	public function rewind(){
	reset($this->_btn);
	}#m rewind

	public function current(){
	return /* $var = */ current($this->_btn);
	}#m current

	public function key(){
	return /* $var = */ key($this->_btn);
	}#m key

	public function next(){
	return /* $var =*/ next($this->_btn);
	}#m next

	public function valid(){
	return /* $var = */ ($this->current() !== false);
	}#m valid

/**#########################################################
* Private and protected methods
*#########################################################*/

	/**
	* Compares two nodes by fnmatch() all properties in $node1
	*
	* @param	Object(backtraceNode)	$toCmp Node compare to
	* @return	integer. 0 if equals. Other otherwise (> or < not defined, but *may be* done later).
	**/
	public function FnmatchCmp(backtraceNode $toCmp){
		foreach($toCmp as $key => $prop){
			if (!isset($this->$key) or !fnmatch($prop, $this->$key)) return 1;
		}
	return 0;	#FnmatchEquals!
	}#m FnmatchCmp

	/**
	* Set format to formatArgs. Array by type of out as key {@see OS::OUT_* constants}, and values as array in format,
	*	as described in {@see class HuFormat}. {@example Debug/_HuFormat.defaults/backtrace::printout.php}
	*	On time of set format NOT CHECKED!
	*
	* @param	array	$format
	* @return	nothing
	**/
	public function setArgsFormat($format){
	$this->_format = REQUIRED_VAR($format);
	}#m setArgsFormat

	/**
	* Return string of formated args
	*
	* @param	array(null)	$format
	*	If null, trying from ->_format set in {@see ::setSrgsFormat()}, and finaly
	*		get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param	integer		$OutType	If present - determine type of format from $format (passed or default). Must be index in $format.
	* @return	string
	* @Throw(VariableArrayInconsistentException)
	**/
	public function formatArgs($format = null, $OutType = null){
	$OutType = ((null === $OutType) ? OS::getOutType() : $OutType); #Caching
	$format = REQUIRED_VAR(
		EMPTY_VAR(
			$format
			,$this->_format[$OutType]['argtypes']
			,@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]['argtypes']
			,
				#Trying include. Conditional ternar operator only for doing include inplace. Parentness () around include is mandatory!!!
				( (include_once('Debug/_HuFormat.defaults/backtrace::printout.php')) || true )
				?
				#Again provide its value. If it now present - cool, if not - REQUIRED_VAR thor exception
				@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]['argtypes']
				: # Only for compatibility with old version which don't support short (cond ?: then) version
				null
		)
	);
//	$format = REQUIRED_VAR(EMPTY_VAR($format, $GLOBALS['__CONFIG']['backtrace::printout'][OS::getOutType()]['argtypes']));
//??	$format = REQUIRED_VAR(EMPTY_VAR($format, $GLOBALS['__CONFIG']['backtrace::printout']['']['argtypes']));
	$args = '';
	$hf = new HuFormat;

		foreach ($this->args as $var){
			if (!empty($args)) $args .= ', ';

			if (isset($format[gettype($var)])){
			$form =& $format[gettype($var)];
			}
			elseif(isset($format['default'])){
			$form =& $format['default'];
			}
			else throw new VariableArrayInconsistentException('Format of type '.gettype($var).' not found. "default" also not provided in $format');

		$hf->set($form, $var);
		$args .= $hf->getString();
		}
	return $args;
	}#m formatArgs
}#c backtraceNode

#########################################
class backtrace implements Iterator{
private $_bt = array();

private $_curNode = 0;
protected $_format;

	/**
	* Constructor
	*
	* @param	array	$bt	Array as result debug_backtrace() or it part. If null filled by
	*	direct debug_backtrace() call.
	* @param	int(1)	$removeSelf	If filled automaticaly, containts also this call
	*	(or call ::create() if appropriate). This will remove it. Number is amount of arrays
	*	remove from stack.
	* @return	Object(backtrace)
	**/
	public function __construct(array $bt = null, $removeSelf = 1){
		if ($bt) $this->_bt = $bt;
		else $this->_bt = debug_backtrace();

		while ($removeSelf--) array_shift($this->_bt);
	}#m __construct

	/**
	* To allow constructions like: backtrace::create()->methodName()
	*
	* @param	array	$bt	{@link ::__construct}
	* @param	int(2)	$removeSelf	{@link ::__construct}
	* @return	backtrace
	**/
	static public function create(array $bt = null, $removeSelf = 2){
	return new self($bt, $removeSelf);
	}#m create

	/**
	* Dump in appropriate(auto) form bactrace.
	*	Fast dump of current backtrase may be invoked as backtrace::create()->dump();
	*
	* @deprecated since 2.1.5.1
	* @param	boolean	$return
	* @param	string	$header('_debug_bactrace()')
	* @return	mixed	return auto::a(...)
	**/
	public function dump($return = false, $header = '_debug_bactrace()'){
	include_once('Debug/debug.php');
	return dump::a($this->_bt, $header, $return);
	}#m dump

	/**
	* Get BackTraceNode by its number
	*
	* @param	integer	$N - Number of interested Node
	* @return	Object(backtraceNode)
	* @Throw(VariableRangeException)
	**/
	public function getNode($N){
		if (isset($this->_bt[ $N = $this->getNumberOfNode($N) ])){
			if (is_array($this->_bt[ $N ])){
			//Cache on fly!!!
			$this->_bt[ $N ] = new backtraceNode($this->_bt[$N], $N);
			}
		//instanceof backtraceNode
		return $this->_bt[$N];
		}
		else throw new VariableRangeException('Needed BackTraceNode not found in this BackTrace!');
	}#m getNode

	/**
	* Replace (or silently add) node in place $N
	*
	* @param	integer	$N	Place to node. If not exists - silently create.
	*	{@see ::getNumberOfNode() fo more description}
	* @return	nothing
	**/
	public function setNode($N, backtraceNode $node){
	$this->_bt[ $this->getNumberOfNode($N) ] = $node;	
	}#m setNode

	/**
	* Return real number of requested Node in _bt array, implements next logic:
 	*	If $N === null set on current node ({@see ::current()}).
	*	If $N < 0	Negative values to to refer in backward: -2 mean: sizeof(debug_backtrace() - 2)!
	*		Be carefull value -1 meaning LAST element, not second from end!
	* 
	* @param	integer	$N
	* @return	integer	Number of requested node.
	**/
	private function getNumberOfNode($N){
	return ( (null !== $N) ? ($N >= 0 ? $N : $this->length() + $N) : $this->key() );
	}#m getNumberOfNode

	/**
	* Delete node in place $N
	* After delete, all indexes is recomputed. BUT, current position not changed!
	* So, be carefully in loops - it may have undefined behavior.
	*
	* @param	integer	$N	Place of node.
	*	{@see ::getNumberOfNode() for more details}
	* @return	nothing
	* @Throw(VariableRangeException)
	**/
	public function delNode($N = null){
		if (!isset($this->_bt[ $calcN = $this->getNumberOfNode($N)])){
		throw new VariableRangeException($N.' node not found! Can\'t delete!');
		}
		else{
		//Do NOT use unset, because it left old keys
		array_splice($this->_bt, $calcN, 1);
		}
 	}#m delNode

	/**
	* Return count of BackTraceNodes.
	*
	* @return	integer
	**/
	public function length(){
	return sizeof($this->_bt);
	}#m length

	/**
	* Find node of bactrace. To match each possible used fnmatch (http://php.net/fnmatch), 
	* so all it patterns and syntrax allowed.
	*
	* @param	Object(backtraceNode)	$need	Parameters to search:
	* 	array(
	*		'file'	=> "*backtrace.php"
	*		'class'	=> "dump"
	*		'function'=> "[aw]"
	*		'type'	=> "->"
	*	)
	* Array may contain next elements, each compared as *strings*: file, line, function, class,
	* object (yes it is, also compared as string, so it may have a sence if implemented __toString
	* magic method on it), type.
	*	Args and N may be present, but first is stupidly compare as string ('Array' === 'Array' :))
	* and to search by N use ::getNode() this faster.
	* @return	Object(backtrace)
	**/
	public function find(backtraceNode $need){
	$ret = clone $this;
	
	//Foreach is dangerous, because we delete elements.
	$ret->rewind();
		while ($node = $ret->current()){
			#Returned 0 if equals
			if ($node->FnmatchCmp($need) != 0){
			$ret->delNode();
			}
			else{
			$node = $ret->next();
			}
		}
	return $ret;
	}#m find

	/**
	* @todo Implement RegExp find. Not now.
	**/
	public function findRegexp(backtraceNode $need){
	throw new BaseException('Method findRegexp not implemented now!');
	}#m findRegexp

	/**
	* Getted (and modifiyed) from http://php.rinet.ru/manual/ru/function.debug-backtrace.php
	* comments of users
	*
	* @param	boolean(false)	$return	Return or print directly.
	* @param	array(null)	$format
	*	If null, trying from format set in {@see ::setPrintoutFormat()}, and finaly
	*		get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param	integer(null)	$OutType	If present - determine type of format from $format (passed or default). Must be index in $format.
	* @Throw(VariableRequiredException, BacktraceEmptyException)
	**/
	public function printout($return = false, array $format = null, $OutType = null){
	$OutType = ((null === $OutType) ? OS::getOutType() : $OutType); #Caching
	$format = REQUIRED_VAR(
		EMPTY_VAR(
			$format
			,$this->_format[$OutType]
			,@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]
			,
			(
				#Trying include. Conditional ternar operator only for doing include inplace. Parentness () around include is mandatory!!!
				( (include_once('Debug/_HuFormat.defaults/backtrace::printout.php')) || true )
				?
				#Again provide its value. If it now present - cool, if not - REQUIRED_VAR thor exception
				@$GLOBALS['__CONFIG']['backtrace::printout'][$OutType]
				: # Only for compatibility with old version which don't support short (cond ?: then) version
				null
			)
		)
	);

		if ($this->_bt){
		$hf = new HuFormat($format, $this);
		$ret = $hf->getString();
		}
		else{
		throw new BacktraceEmptyException(new backtrace, '$this->_bt', 'Backtrace is empty! Nothing to printout!');
		}

		if ($return) return $ret;
		else echo $ret;
	}#m printout

	/**
	* Set format to printout. Array by type of out as key {@see OS::OUT_* constants}, and values as array in format,
	*	as described in {@see class HuFormat}. {@example Debug/_HuFormat.defaults/backtrace::printout.php}
	*	On time of set format NOT CHECKED!
	*
	* @param	array	$format
	* @return	&$this
	**/
	public function &setPrintoutFormat($format){
	$this->_format = REQUIRED_VAR($format);
	return $this;
	}#m setPrintoutFormat

	/**
	* By default convert into string will ::printout();
	*
	* @return string
	**/
	public function __toString(){
	return $this->printout(true);
	}#m __toString

/**##################################################################
* From interface Iterator
* Use self indexing to allow delete nodes and continue loop foreach.
###################################################################*/
	/**
	* Rewind internal pointer to begin
	*
	* @return	nothing
	**/
	public function rewind(){
	$this->_curNode = 0;
	}#m rewind

	/**
	* Return current backtraceNode
	*
	* @return	Object(backtraceNode)|null
	**/
	public function current(){
		try{
		return $this->getNode($this->_curNode);
		}
		catch (VariableRangeException $vre){
		return null;
		}
	}#m current

	/**
	* Return current key
	*
	* @return	integer
	**/
	public function key(){
	return $this->_curNode;
	}#m key

	/**
	* Return next backtraceNode
	*
	* @return	Object(backtraceNode)|null
	**/
	public function next(){
		try{
		return $this->getNode( ++$this->_curNode );
		}
		catch (VariableRangeException $vre){
		return null;
		}
	}#m next

	/**
	* Return if Iterator valid and not end reached.
	*
	* @return	boolean
	**/
	public function valid(){
	return ($this->current() !== null);
	}#m valid

	/**
	* Return end backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	*
	* @return	Object(backtraceNode)
	**/
	public function end(){
	return $this->getNode( ($this->_curNode = $this->length() - 1) );
	}#m end

	/**
	* Return prev backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	*
	* @return	Object(backtraceNode)|null
	**/
	public function prev(){
		if ($this->_curNode < 1) return null;

	return $this->getNode( --$this->_curNode );
	}#m prev
}#c backtrace
?>