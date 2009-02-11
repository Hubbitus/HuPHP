<?
/**
* RegExp manupulation.
* @package RegExp
* @version 2.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-29
*	- Separate classes RegExp_base_base and RegExp_base to allov using this on PHP < 5.3.0-dev
*	- Add doc to methods, reformatting.
*
*	* 2008-05-30 19:05
*	- Made $paireddelimeters method NOT static. It is allowed in implementation, because it is may
*		now be used as property. So, if outsource code use it static - must change it. This is sacrifice
*		to to compatibility with PHP < 5.3.0 (whithout late static bindings)
*	- getMatch add in eval-code, to avoid fatal errors in earler versions PHP
*
*	* 2009-01-18 14:57 (No version bump)
*	- Reflect renaming Class.php to HuClass.php
*
*	* 2009-01-18 23:39 ver 2.1b to 2.1
*	- Add method getText in base class

*	* 2009-02-11 13:41 ver 2.1 to 2.1.1
	- Add method split
**/

include_once('macroses/EMPTY_STR.php');
include_once('macroses/REQUIRED_NOT_NULL.php');

include_once('Vars/HuClass.php');	#To method "create"

abstract class RegExp_base_base extends HuClass{
#MUST be defined properly in childs
const className = 'RegExp_base';

protected $sourceText;
protected $RegExp;

protected $matchCount;
protected $matches;
protected $matchesValid = false;

protected	$replaceTo;
protected $replaceRes;
protected $replaceValid;

#array of paired delimeters, where start not equals end. Key is start delimiter.
public $paireddelimeters = array(
	'{' => '}',
	'<' => '>',
	'(' => ')',
	'[' => ']',
);

	/**
	* Aka __construct, but for static call.
	* 
	* Primarly needed to create object of future defined class in base (see getMatch method)
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
	}#_c

	/**
	* Return N-th single match
	*
	* @param int	$Number Number of interesting match
	* @return string|array
	**/
	public function match($Number){
		if (!$this->matchesValid)#May be throw Exception???
		$this->doMatch();

	return $this->matches[$Number];
	}#m match

	/**
	* Return regexp string.
	*
	* @return string
	**/
	public function getRegExp(){
	return $this->RegExp;
	}#m getRegExp

	/**
	* Set RegExp from string.
	*
	* @param string|array	$regexp
	* @return &$this
	**/
	public function &setRegExp($regexp){
	$this->RegExp = REQUIRED_NOT_NULL($regexp);
	$this->matchesValid = false;
	return $this;
	}#m setRegExp

	/**
	* Return current text.
	*
	* @return string
	**/
	public function getText(){
	return $this->sourceText;
	}#m getText

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
	}#m setText

	/**
	* Equivalent of {@see ->&setText()}, but assign text by ref. Be very carefully!
	*
	* @param string	$text
	* @return &$this
	**/
	public function &setTextRef(&$text){
	$this->sourceText = $text;
	$this->matchesValid = false;
	return $this;
	}#m setTextRef

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
	}#m setReplaceTo

	/**
	* Return count of matches. If matches not valid - by default do ::doMatchAll() first
	*
	* @return integer
	**/
	public function matchCount(){
		if (!$this->matchesValid)#May be throw Exception???
		$this->doMatchAll();
	return $this->matchCount;
	}#m MatchCount

	/**
	* Set Pattern, text, raplacement. Shorthand to appropriate methods.
	*
	* @param string|array	$regexp
	* @param string		$Text
	* @param string|array	$text
	* @return	&$this
	**/
	public function &set($RegExp = null, $Text = null, $replaceTo = null){
//	$this->setRegExp(EMPTY_VAR($regexp, ''))->setText(EMPTY_STR($text, ''))->setReplaceTo(EMPTY_VAR($replaceTo, ''));
		foreach (array('RegExp', 'Text', 'replaceTo') as $v){
			if ($$v) $this->{"set$v"} ($$v);
		}
	return $this;
	}#m set

	/**
	* Do test, faster then doMatch, don't filling ->matches, ->matchCount and other.
	**/
	abstract public function test();#{}#m test

	/**
	* Description of $flags and $offset see on http://www.php.net/preg_match_all
	* Called by default, in ->match()!
	*
	* @return &$this
	**/
	abstract public function &doMatch($flags = null, $offset = null);

	/**
	* {@see ->doMatch()}. But match all occurences.
	*
	* @return &$this
	**/
	abstract public function &doMatchAll($flags = null, $offset = null);

	/**
	* Return startDelimiter
	*
	* @param integer $item. If not null - pount to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return char
	**/
	public function getRegExpDelimiterStart($item = null){
	$item = is_null($item) ? 0 : $item;
		if (is_array($this->RegExp)) return $this->RegExp[$item]{0};
		else return $this->RegExp{0};
	}#m getRegExpDelimiterStart

	/**
	* Return endDelimiter
	*
	* @param integer	$item. If not null - pount to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return char
	**/
	public function getRegExpDelimiterEnd($item = null){
		if (isset($this->paireddelimeters[$this->getRegExpDelimiterStart($item)]))
		return $this->paireddelimeters[$this->getRegExpDelimiterStart($item)];
		else return $this->getRegExpDelimiterStart($item);
	}#m getRegExpDelimiterEnd

	/**
	* Assume RegeExp correct. Do not check it.
	*
	* @param integer	$item. If not null - pount to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return string
	**/
	public function getRegExpBody($item = null){
	$item = is_null($item) ? 0 : $item;
		if (is_array($this->RegExp)) return substr($this->RegExp[$item], 1, strrpos($this->RegExp[$item], $this->getRegExpDelimiterEnd($item)) - 1);
		else return substr($this->RegExp, 1, strrpos($this->RegExp, $this->getRegExpDelimiterEnd()) - 1);
	}#m getRegExpBody

	/**
	* Return RegExpModifiers
	*
	* @param integer	$item. If not null - pount to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return char
	**/
	public function getRegExpModifiers($item = null){
	$item = is_null($item) ? 0 : $item;
		if (is_array($this->RegExp)) return (string)substr($this->RegExp[$item], strrpos($this->RegExp[$item], $this->getRegExpDelimiterEnd($item)) + 1 );
		else return (string)substr($this->RegExp, strrpos($this->RegExp, $this->getRegExpDelimiterEnd()) + 1 );
	}#m getRegExpModifiers

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
	* Quote given string or each (recursive) string in array.
	*
	* @param	string|array	$toQuote
	* @param	string='/'	$delimiter. Chars to addition escape. Usaly (and default) char start and end of regexp.
	* @return	string|array	Same type as given.
	**/
	abstract public static function quote($toQuote, $delimeter = '/');

	/**
	* Full array of matches after call (not checked!) {@see doMatch()}, {@see doMatchAll()}, {@see split()}
	*
	* @return array of last matches.
	**/
	public function getMatches(){
	return $this->matches;
	}#m getMatches
}#c RegExp_base_base

	/**
	* Require late-static-bindings future, so, it is available only in PHP version >= 5.3.0-dev
	**/
	if (version_compare(PHP_VERSION, '5.3.0-dev', '>=')){
	//eval to avoid fatal error on earler versions
	eval ( '
		abstract class RegExp_base extends RegExp_base_base{
		/** Return string, matching Regexp
		* $N - No of subpattern of regexp, 0 meen - match all regular expression
		* for fast static call
		**/
			public static function getMatch($regexp, $text, $N=0){
			//$tmpR = new self::$className($regexp, $text);
			/**
			* Require using static:: instead of self::. See
			* http://ru2.php.net/manual/ru/language.oop5.static.php single
			* comment from "gabe at mudbugmedia dot com" and also
			* http://www.colder.ch/news/08-24-2007/28/late-static-bindings-expl.html
			* This only works on PHP vrom version 5.3.0
			**/
			//Additionaly new static::className($regexp, $text); DO NOT work, so using one more variable
			//$tmpR = new static::className($regexp, $text);
			$className = static::className;
			$tmpR = new $className($regexp, $text);
			$tmpR->doMatch();
			return $tmpR->match($N);
			}#m getMatch
		}
	    '
	);
	}
	else{
		abstract class RegExp_base extends RegExp_base_base{
			public static function getMatch($regexp, $text, $N=0){
			throw new ClassMethodExistsException ('RegExp_base::getMatch not implemented for this version of PHP!');
			}
		}
	}
?>