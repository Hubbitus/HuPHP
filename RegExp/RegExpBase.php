<?php
declare(strict_types=1);

/**
* RegExp manipulation.
*
* @package RegExp
* @version 2.1.2.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2014, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-29
*
* @uses VariableIsNullException
* @uses HuClass
* @uses HuArray
**/

namespace Hubbitus\HuPHP\RegExp;

use Hubbitus\HuPHP\Vars\HuClass;
use Hubbitus\HuPHP\Vars\HuArray;
use function Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL;

abstract class RegExpBase extends HuClass implements IRegExp {
	protected $sourceText;
	protected $RegExp;

	protected $matchCount;
	protected $matches;
	protected $matchesValid = false;

	protected $replaceTo;
	protected $replaceRes;
	protected $replaceValid;

	// array of paired delimiters, where start not equals end. Key is start delimiter.
	public $paireddelimeters = array(
		'{' => '}',
		'<' => '>',
		'(' => ')',
		'[' => ']',
	);

	/**
	* Aka __construct, but for static call.
	*
	* Primarily needed to create object of future defined class in base (see getMatch method)
	* Derived from HuClass::create
	*
	* @method create()
	* @return Object(RegExp_base)
	**/

	/**
	* Constructor.
	*
	* For parameters {@see ->set()}
	**/
	public function __construct($regexp = null, $text = null, $replaceTo = null){
		$this->set($regexp, $text, $replaceTo);
	}
	/**
	* Return N-th single match
	*
	* @param int	$Number Number of interesting match
	* @return string|array
	**/
	public function match($Number){
		if (!$this->matchesValid) // May be throw Exception???
			$this->doMatch();

		return $this->matches[$Number];
	}
	/**
	* Return regexp string.
	*
	* @return string
	**/
	public function getRegExp(){
		return $this->RegExp;
	}
	/**
	* Set RegExp from string.
	*
	* @param string|array	$regexp
	* @return &$this
	* @thrown VariableIsNullException
	**/
	public function &setRegExp($regexp): static {
		$this->RegExp = REQUIRED_NOT_NULL($regexp);
		$this->matchesValid = false;
		return $this;
	}
	/**
	* Return current text.
	*
	* @return string
	**/
	public function getText(){
		return $this->sourceText;
	}
	/**
	* Set text to match from string.
	*
	* @param string	$text
	* @return &$this
	**/
	public function &setText($text){
		$this->sourceText = REQUIRED_NOT_NULL($text);
		$this->matchesValid = false;
		return $this;
	}
	/**
	* Equivalent of {@see ->&setText()}, but assign text by ref. Be very carefully!
	*
	* @param string	$text
	* @return &$this
	**/
	public function &setTextRef(&$text){
		$this->sourceText =& $text;
		$this->matchesValid = false;
		return $this;
	}
	/**
	* Set ReplaceTo
	*
	* @param string|array	$text
	* @return &$this
	**/
	public function &setReplaceTo($text){
		$this->replaceTo = REQUIRED_NOT_NULL($text);
		$this->replaceValid = $this->matchesValid = false;
		return $this;
	}
	/**
	* Return count of matches. If matches not valid - by default do ::doMatchAll() first
	*
	* @return integer
	**/
	public function matchCount(){
		if (!$this->matchesValid) // May be throw Exception???
			$this->doMatchAll();
		return $this->matchCount;
	}
	/**
	* Set Pattern, text, replacement. Shorthand to appropriate methods.
	*
	* @param string|array	$regexp
	* @param string		$Text
	* @param string|array	$text
	* @return	&$this
	**/
	public function &set($RegExp = null, $Text = null, $replaceTo = null){
		foreach (array('RegExp', 'Text', 'replaceTo') as $v){
			if ($$v) $this->{"set$v"} ($$v);
		}
		return $this;
	}
	/**
	* Do test, faster then doMatch, don't filling ->matches, ->matchCount and other.
	**/
	abstract public function test();
	/**
	* Description of $flags and $offset see on http://www.php.net/preg_match_all
	* Called by default, in ->match()!
	*
	* @return &$this
	**/
	abstract public function &doMatch($flags = null, $offset = null);

	/**
	* {@see ->doMatch()}. But match all occurrences.
	*
	* @return &$this
	**/
	abstract public function &doMatchAll($flags = null, $offset = null);

	/**
	* Return startDelimiter
	*
	* @param integer $item. If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return string
	**/
	public function getRegExpDelimiterStart($item = null){
		$item = is_null($item) ? 0 : $item;
		if (is_array($this->RegExp)) return $this->RegExp[$item][0];
		else return $this->RegExp[0];
	}
	/**
	* Return endDelimiter
	*
	* @param integer	$item. If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return string
	**/
	public function getRegExpDelimiterEnd($item = null){
		if (isset($this->paireddelimeters[$this->getRegExpDelimiterStart($item)]))
			return $this->paireddelimeters[$this->getRegExpDelimiterStart($item)];
		else
			return $this->getRegExpDelimiterStart($item);
	}
	/**
	* Assume RegExp correct. Do not check it.
	*
	* @param integer	$item. If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return string
	**/
	public function getRegExpBody($item = null){
		$item = is_null($item) ? 0 : $item;
		if (is_array($this->RegExp)) return substr($this->RegExp[$item], 1, strrpos($this->RegExp[$item], $this->getRegExpDelimiterEnd($item)) - 1);
		else return substr($this->RegExp, 1, strrpos($this->RegExp, $this->getRegExpDelimiterEnd()) - 1);
	}
	/**
	* Return RegExpModifiers
	*
	* @param integer	$item. If not null - point to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return string
	**/
	public function getRegExpModifiers($item = null){
		$item = is_null($item) ? 0 : $item;
		if (is_array($this->RegExp)) return (string)substr($this->RegExp[$item], strrpos($this->RegExp[$item], $this->getRegExpDelimiterEnd($item)) + 1 );
		else return (string)substr($this->RegExp, strrpos($this->RegExp, $this->getRegExpDelimiterEnd()) + 1 );
	}
	/**
	* Description see {@link http://php.net/preg_replace}
	*
	* @param int	$limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @return mixed	Replaced value.
	**/
	abstract public function replace($limit = -1);

	/**
	* Split by regexp. Results as usual in matches.
	*
	* @since Version 2.1.1
	*
	* @param int(-1)	$limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @param int(null)	$flags Flags for the operation.
	* @return &$this
	**/
	abstract public function &split($limit = -1, $flags = null);

	/**
	* Full(os sub, if $n present) array of matches after call (not checked!) {@see doMatch()}, {@see doMatchAll()}, {@see split()}
	*
	* @param	int|null	Number of sub array
	* @return array of last matches.
	**/
	public function getMatches($n = null){
		if (is_null($n)) return $this->matches;
		else return $this->matches[$n];
	}
	/**
	* Full equivalent of {@see getMatches()) except of result returned as Object(HuArray) instead of regular array.
	*
	* @param	int|null	Directly passed to {@see getMatches}
	* @return HuArray of last matches.
	**/
	public function getHuMatches($n = null){
		return new HuArray($this->getMatches($n));
	}
}
