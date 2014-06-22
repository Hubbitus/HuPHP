<?
/**
* Debug and backtrace toolkit.
*
* In call function funcName($currentValue); in any place, in function by other methods available only
* value of variable $currentValue but name call-time (in this example '$currentValue') - NOT.
*
* This return array of names CALL parameters!
* Implementation is UGLY - view in source PHP files and parse it, but I NOT known other way!!!
*
* In number of array in debug_backtrace().
*
*, like this:
*Array(
*	[file] => /var/www/vkontakte.nov.su/backends/postMessageReply.php
*	[line] => 22
*	[function] => REQUIRED_VAR
*	[args] => Array(
*		[0] =>
*		)
*)
*
* I cannot do that easy in Regular Expression, due to possible call like this:
* t($tt,
* 	$ttt[0]
* 	,$ttt['qaz']
* 				,tttt(),
*
*				"exampleFunc() call")
* ;
*
* $db[$N]['line'] refer to string with closing call ')' :(.
* Now search open string number. And then from it string, by function name tokenize all what me need.
*
* @package Debug
* @version 2.1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-18 17:44 ver 2.1 to 2.1.1
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses backtrace
* @uses RegExp_pcre
* @uses file_inmem
**/

include_once('macroses/REQUIRED_VAR.php');

	if (!defined('T_ML_COMMENT')) {
		define('T_ML_COMMENT', T_COMMENT);
	} else {
		define('T_DOC_COMMENT', T_ML_COMMENT);
	}

class Tokenizer{
	private /* backtraceNode */ $_debugBacktrace = null;

	protected $_filePhpSrc = null;
	private $_callStartLine = 0;
	private $_callText = '';
	private $_tokens = null;
	private $_curTokPos = 0;
	private $_args = array();
	private $_regexp = null;

	/**
	* Constructor.
	*
	* @param array|Object(backtraceNode) $db	Array, one of is subarrays from return result by debug_backtrace();
	* @return $this
	**/
	public function __construct(/* array | backtraceNode */ $db = array()){
		if (is_array($db)) $this->setFromBTN(new backtraceNode($db));
		$this->setFromBTN($db);
	}#__c

	/**
	* Set from Object(backtraceNode).
	*
	* {@inheritdoc ::__construct()}
	* @return &$this
	**/
	public function &setFromBTN(backtraceNode $db){
		$this->clear();
		$this->_debugBacktrace = $db;
		return $this;
	}#m setFromBTN

	/**
	* To allow constructions like: Tokenizer::create()->methodName()
	* {@inheritdoc ::__construct()}
	**/
	static public function create(/* array | backtraceNode */ $db){
		return new self($db);
	}#m create

	/**
	* Clear object
	*
	* @return nothing
	**/
	public function clear(){
		$this->_debugBacktrace = null;
		$this->_filePhpSrc = null;
		$this->_callStartLine = 0;
		$this->_callText = '';
		$this->_tokens = null;
		$this->_curTokPos = 0;
		$this->_args = array();
		$this->_regexp = null;
	}#m clear

	/**
	* Return string of parsed argument by it number (index from 0). Bounds not checked!
	*
	* @param integer $n - Number of interesting argument.
	* @return string
	**/
	public function getArg($n, $trim = true){
		if ($trim) return trim($this->_args[$n]);
		else return $this->_args[$n];
	}#m getArg

	/**
	* Set to arg new value.
	*
	* @param	integer	$n - Number of interesting argument. Bounds not checked!
	* @param	mixed	$value Value to set.
	* @return	&$this
	**/
	public function &setArg($n, $value){
		$this->_args[$n] = $value;
		return $this;
	}#m setArg

	/**
	* Return array of all parsed arguments.
	*
	* @return array
	**/
	public function getArgs(){
		return $this->_args;
	}#m getArgs

	/**
	* Return count of parsed arguments.
	*
	* @return integer
	**/
	public function countArgs(){
		return sizeof($this->_args);
	}#m countArgs

	/**
	* Search full text of call in src php-file
	*
	* @return $this
	* @Throws(VariableRequiredException)
	**/
	protected function findTextCall(){
		$this->_filePhpSrc = new file_inmem(REQUIRED_VAR($this->_debugBacktrace->file));
		$this->_filePhpSrc->loadContent();

		$rega = '/'
			.RegExp_pcre::quote(@$this->_debugBacktrace->type) // For classes '->' or '::'. For regular functions not exist.
			.'\b'.$this->_debugBacktrace->function // In case of method and regular function same name present.
			.'\s*\((.*?)\s*\)' // call
			.'/xms';

			$this->_regexp = new RegExp_pcre($rega, $this->_filePhpSrc->getBLOB());
		$this->_regexp->doMatchAll(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$this->_regexp->convertOffsetToChars(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		return $this;
	}#m findTextCall

	/**
	* See description on begin of file ->_debugBacktrace->line not correct start call-line if call
	* continued on more then one string!
	* Seek closest-back line from found matches. In other words, search start of call.
	* So, in any case, I do not have chance separate calls :( , if it presents more then one in string!
	* Found and peek first call in string, other not handled on this moment.
	*
	* @return &$this;
	**/
	protected function &findCallStrings(){
		if (!$this->_regexp) $this->findTextCall();

		$delta = PHP_INT_MAX;
		$this->_callStartLine = 0;

		//Search closest line
		foreach ($this->_regexp->getMatches() as $k => $match){
			$lineN = $this->_filePhpSrc->getLineByOffset($match[0][1]) + 1; //Indexing from 0
				if ( ($d = $this->_debugBacktrace->line - $lineN) >= 0 and $d < $delta){
					$delta = $d;
					$this->_callStartLine = $lineN;
				}
				else break;//Not needed more
			}

			$this->_callText = implode(
				$this->_filePhpSrc->getLineSep()
				,$this->_filePhpSrc->getLines(
					array(
						$this->_callStartLine - 1
						,$delta + 1
					)
				)
			);
	return $this;
	}#m findCallStrings

	/**
	* Parse tokens
	*
	* @return &$this
	**/
	public function &parseTokens(){
		if (!$this->_callText) $this->findCallStrings();

		// Without start and end tags not parsed properly.
		$this->_tokens = token_get_all('<?' . $this->_callText . '?>');
		return $this;
	}#m parseTokens

	/**
	* Working horse!
	* Base idea from: http://ru2.php.net/manual/ru/ref.tokenizer.php
	*
	* @param boolean(true) $stripWhitespace = False! Because stripped any space, not only on
	*	start and end of arg! This is may be not wanted behavior on constructions like:
	*	$a instance of A. Instead see option $trim in {@link ::getArg()) method.
	* @param boolean(false) $stripComments = false
	* @return $this
	**/
	public function &parseCallArgs($stripWhitespace = false, $stripComments = false){
		if ($this->_tokens === null) $this->parseTokens();

		$this->skipToStartCallArguments();
		$this->addArg();
		$sParenthesis = 0; //stack
		$sz = sizeof($this->_tokens);
			while ($this->_curTokPos < $sz){
				$token =& $this->_tokens[$this->_curTokPos++];

				if (is_string($token)){
					switch($token){
						case '(':
							++$sParenthesis;
							// Self ( - do not want
							if ($sParenthesis > 1) $this->addToArg($token);
						break;

						case ')':
							--$sParenthesis;
							if (0 == $sParenthesis) break 2;
							$this->addToArg($token);
							break;

						case ',':
							if (1 == $sParenthesis) $this->addArg();
							else $this->addToArg($token);
							break;

						default:
							$this->addToArg($token);
					}
				}
				else{
					switch($token[0]){
						case T_COMMENT:
						case T_ML_COMMENT:	// we've defined this
						case T_DOC_COMMENT:	// and this
							if (!$stripComments) $this->addToArg($token[1]);
							break;

						case T_WHITESPACE:
							if (!$stripWhitespace) $this->addToArg($token[1]);
							break;

						default:
							$this->addToArg($token[1]);
					}
				}
			}
		return $this;
	}#m parseCallArgs

	/**
	* Move ->_curTokPos to first tokens after functionName(
	*
	* @return $this
	**/
	private function skipToStartCallArguments(){
		$sz = sizeof($this->_tokens);
			while ($this->_curTokPos < $sz){
				$token =& $this->_tokens[$this->_curTokPos++];
				if (is_array($token) and T_STRING == $token[0] and $token[1] == $this->_debugBacktrace->function)
					return;
			}
		return $this;
	}#m skipToStartCallArguments

	/**
	* Add text to CURRENT arg.
	*
	* @return noting
	**/
	private function addToArg($str){
		$this->_args[$this->countArgs() - 1] .= $str;
	}#m addToArg

	/**
	* Add next arg to array
	*
	* @return nothing
	**/
	private function addArg(){
		$this->_args[$this->countArgs()] = '';
	}#m addArg

	/**
	* Strip quotes on start and end of argument.
	* Paired
	*
	* @param	string	$arg	Argument to process.
	* @param	boolean	$all If true - all trim, else (by default) - only paired (if only ended with quote, or only started - leaf it as is).
	* @return	string
	**/
	static public function trimQuotes($arg, $all = false){
		if (!$arg) return '';

		$len = strlen($arg);
		if ('"' == $arg{0} or '\'' == $arg{0}) $from = 1;
		else $from = 0;
		if ('"' == $arg{$len-1} or '\'' == $arg{$len-1}) $len -= (1 + $from);

		if ($all) return (substr($arg, $from, $len));
		elseif(strlen($arg) - $len > 1) return (substr($arg, $from, $len));
		else return $arg;
	}#m trimQuotes
}#c Tokenizer
?>