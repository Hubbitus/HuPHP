<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\BacktraceNode;
use Hubbitus\HuPHP\RegExp\RegExpPcre;
use Hubbitus\HuPHP\Filesystem\FileInMemory;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;

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
* $db[$N]['line'] refer to string with closing call ')' :(.
* Now search open string number. And then from it string, by function name tokenize all what me need.
*
* @package Debug
* @version 2.1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-18 17:44 ver 2.1 to 2.1.1
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses backtrace
* @uses RegExp_pcre
* @uses file_inmem
**/
class Tokenizer {
	private ?BacktraceNode $_debugBacktrace = null;

	protected ?FileInMemory $_filePhpSrc = null;
	private int $_callStartLine = 0;
	private string $_callText = '';
	private ?array $_tokens = null;
	private int $_curTokPos = 0;
	private array $_args = [];
	private ?RegExpPcre $_regexp = null;

	/**
	* Constructor.
	*
	* @param BacktraceNode $btn Backtrace node to tokenize
	* @return void
	**/
	public function __construct(BacktraceNode $btn) {
		$this->setFromBTN($btn);
	}

	/**
	* Set from Object(BacktraceNode).
	*
	* @param BacktraceNode $btn Backtrace node to tokenize
	* @return $this
	**/
	public function setFromBTN(BacktraceNode $btn): static {
		$this->clear();
		$this->_debugBacktrace = $btn;
		return $this;
	}

	/**
	* To allow constructions like: Tokenizer::create()->methodName()
	*
	* @param BacktraceNode $btn Backtrace node to tokenize
	* @return static
	**/
	public static function create(BacktraceNode $btn): static {
		/** @phpstan-ignore new.static */
		return new static($btn); // Tokenizer is not final, static() is safe
	}

	/**
	* Clear object
	**/
	public function clear(): void {
		$this->_debugBacktrace = null;
		$this->_filePhpSrc = null;
		$this->_callStartLine = 0;
		$this->_callText = '';
		$this->_tokens = null;
		$this->_curTokPos = 0;
		$this->_args = [];
		$this->_regexp = null;
	}

	/**
	* Return string of parsed argument by it number (index from 0). Bounds not checked!
	*
	* @param int $n Number of interesting argument.
	* @param bool $trim Trim result
	* @return string
	**/
	public function getArg(int $n, bool $trim = true): string {
		if ($trim) return \trim($this->_args[$n]);
		else return $this->_args[$n];
	}

	/**
	* Set to arg new value.
	*
	* @param int $n Number of interesting argument. Bounds not checked!
	* @param mixed $value Value to set.
	* @return $this
	**/
	public function setArg(int $n, mixed $value): static {
		$this->_args[$n] = $value;
		return $this;
	}

	/**
	* Return array of all parsed arguments.
	*
	* @return array
	**/
	public function getArgs(): array {
		return $this->_args;
	}

	/**
	* Return count of parsed arguments.
	**/
	public function countArgs(): int {
		return \sizeof($this->_args);
	}

	/**
	* Search full text of call in src php-file
	*
	* @return $this
	* @throws VariableRequiredException
	**/
	protected function findTextCall(): static {
		$this->_filePhpSrc = new FileInMemory(REQUIRED_VAR($this->_debugBacktrace->file));
		$this->_filePhpSrc->loadContent();

		$reg = '/'
			.RegExpPcre::quote($this->_debugBacktrace->type ?? '') // For classes '->' or '::'. For regular functions not exist.
			.'\b'.RegExpPcre::quote($this->_debugBacktrace->function) // In case of method and regular function same name present.
			.'\s*\((.*?)\s*\)' // call
			.'/xms';

		$this->_regexp = new RegExpPcre($reg, $this->_filePhpSrc->getBLOB());
		$this->_regexp->doMatchAll(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$this->_regexp->convertOffsetToChars(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		return $this;
	}

	/**
	* See description on begin of file ->_debugBacktrace->line not correct start call-line if call
	* continued on more then one string!
	* Seek closest-back line from found matches. In other words, search start of call.
	* So, in any case, I do not have chance separate calls :( , if it presents more then one in string!
	* Found and peek first call in string, other not handled on this moment.
	*
	* @return $this
	**/
	protected function findCallStrings(): static {
		if ($this->_regexp === null) $this->findTextCall();

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

		$this->_callText = \implode(
			$this->_filePhpSrc->getLineSep(),
			$this->_filePhpSrc->getLines([
				$this->_callStartLine - 1,
				(int)($delta + 1),
			])
		);
		return $this;
	}

	/**
	* Parse tokens
	*
	* @return $this
	**/
	public function parseTokens(): static {
		if ('' === $this->_callText) $this->findCallStrings();

		// Without start and end tags not parsed properly.
		$this->_tokens = \token_get_all('Throws' . $this->_callText . '?>');
		return $this;
	}

	/**
	* Working horse!
	* Base idea from: http://ru2.php.net/manual/ru/ref.tokenizer.php
	*
	* @param bool $stripWhitespace Because stripped any space, not only on
	*	start and end of arg! This is may be not wanted behavior on constructions like:
	*	$a instance of A. Instead see option $trim in {@link ::getArg()} method.
	* @param bool $stripComments
	* @return $this
	**/
	public function parseCallArgs(bool $stripWhitespace = false, bool $stripComments = false): static {
		if ($this->_tokens === null) $this->parseTokens();

		$this->skipToStartCallArguments();
		$this->addArg();
		$sParenthesis = 0; //stack
		$sz = \sizeof($this->_tokens);
		while ($this->_curTokPos < $sz){
			$token =& $this->_tokens[$this->_curTokPos++];

			if (\is_string($token)){
				switch($token){
					case '(':
						++$sParenthesis;
						// Self ( - do not want
						if ($sParenthesis > 1) $this->addToArg($token);
						break;

					case ')':
						--$sParenthesis;
						if (0 === $sParenthesis) break 2;
						$this->addToArg($token);
						break;

					case ',':
						if (1 === $sParenthesis) $this->addArg();
						else $this->addToArg($token);
						break;

					default:
						$this->addToArg($token);
				}
			}
			else{
				switch($token[0]){
					case T_COMMENT:
					case T_DOC_COMMENT:
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
	}

	/**
	* Move ->_curTokPos to first tokens after functionName(
	*
	* @return $this
	**/
	private function skipToStartCallArguments(): Tokenizer {
		$sz = \sizeof($this->_tokens);
		while ($this->_curTokPos < $sz){
			$token =& $this->_tokens[$this->_curTokPos++];
			if (\is_array($token) && T_STRING === $token[0] && $token[1] === $this->_debugBacktrace->function)
				return $this;
		}
		return $this;
	}

	/**
	* Add text to CURRENT arg.
	**/
	private function addToArg(string $str): void {
		$this->_args[$this->countArgs() - 1] .= $str;
	}

	/**
	* Add next arg to array
	**/
	private function addArg(): void {
		$this->_args[$this->countArgs()] = '';
	}

	/**
	* Strip quotes on start and end of argument.
	* Paired
	*
	* @param string $arg Argument to process.
	* @param bool $all If true - all trim, else (by default) - only paired (if only ended with quote, or only started - leaf it as is).
	* @return string
	**/
	public static function trimQuotes(string $arg, bool $all = false): string {
		if ($arg === '') return '';

		$len = \strlen($arg);
		$from = ('"' === $arg[0] or '\'' === $arg[0]) ? 1 : 0;
		if ('"' === $arg[$len-1] or '\'' === $arg[$len-1]) $len -= (1 + $from);

		if ($all) {
			return (\substr($arg, $from, $len));
		}
		elseif (\strlen($arg) - $len > 1) {
			return (\substr($arg, $from, $len));
		}
		else {
			return $arg;
		}
	}
}
