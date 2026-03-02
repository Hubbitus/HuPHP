<?php
declare(strict_types=1);

/**
* RegExp manipulation. PCRE-version.
*
* @package RegExp
* @version 2.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-02-11 13:41 ver 2.1 to 2.1.1
**/

namespace Hubbitus\HuPHP\RegExp;

use Hubbitus\HuPHP\Vars\HuArray;

/**
* @uses RegExp_base
**/
class RegExpPcre extends RegExpBase {
	const string className = 'RegExp_pcre';

	/**
	* {@inheritdoc}
	**/
	public function test(){
		return ($this->matchCount = preg_match($this->RegExp, $this->sourceText));
	}
	/**
	* {@inheritdoc}
	**/
	public static function quote($toQuote, $delimiter = '/'){
		if (\is_array($toQuote)){
			\array_walk_recursive($toQuote, function(&$v) use ($delimiter) {
				$v = \preg_quote($v, $delimiter);
			});
			return $toQuote;
		}
		else return \preg_quote($toQuote, $delimiter);
	}
	/**
	* {@inheritdoc}
	**/
	public function &doMatch($flags = PREG_OFFSET_CAPTURE, $offset = 0){
		$this->matchCount = \preg_match($this->RegExp, $this->sourceText, $this->matches, $flags ?? PREG_OFFSET_CAPTURE, $offset ?? 0);
		$this->matchesValid = true;
		//Now must be called manually, if needed! $this->convertOffsetToChars($flags);
		return $this;
	}
	/**
	* {@inheritdoc}
	**/
	public function &doMatchAll($flags = PREG_OFFSET_CAPTURE, $offset = 0){
		$this->matchCount = \preg_match_all($this->RegExp, $this->sourceText, $this->matches, $flags ?? PREG_OFFSET_CAPTURE, $offset ?? 0);
		$this->matchesValid = true;
		//Now must be called manually, if needed! $this->convertOffsetToChars($flags);
		return $this;
	}
	/**
	* Conversion bytes offsets to characters.
	*
	* with PREG_OFFSET_CAPTURE preg_match* returns bytes offset!!!! nor chars!!!!
	* So, recalculate it in chars is several methods:
	* 1) Using utf8_decode. See http://ru2.php.net/manual/ru/function.strlen.php
	*	comment "chernyshevsky at hotmail dot com"
	* 2) And using mb_strlen http://ru2.php.net/manual/ru/function.preg-match.php comment "chuckie"
	*
	* I using combination of its. And it independent of the presence mbstring extension!
	*
	* @param	int(PREG_OFFSET_CAPTURE)	$flags Flags which was used in previous operation.
	* @return	void
	*/
	final public function convertOffsetToChars($flags = PREG_OFFSET_CAPTURE){
		/*
		* A recalculate offset may be done by many ways. See test/strlen_speed_tests.php for more detailes.
		* Short conclusion from this tests are:
		* 1) It is very-very slowly operations, so
		*	1.1) We refusal to do it in any time. This must be called manually if you want (and it also may need binary offset meantime too!!!).
		*	1.2) For that, change access type to public
		* 2) To case when it is needed second conclusion - the most fast way is mb_strlen, but it is not included in core PHP...
		*	2.1) If available, use mb_strlen
		*	2.2) For compatibility, provide fallback to strlen(utf8_decode(...)) (2nd place of speed)
		**/
		if ($this->matchCount and ($flags & PREG_OFFSET_CAPTURE)){
			$func_strlen = fn(string $str) => function_exists('mb_strlen') ? \mb_strlen($str, 'UTF-8') : \strlen(\utf8_decode($str));

			foreach($this->matches as &$match){
				foreach ($match as &$m){
					if (\is_array($m)) {
						$m[1] = $func_strlen(\substr($this->sourceText, 0, (int)$m[1]));
					}
				}
			}
		}
	}
	/**
	* {@inheritdoc}
	* Description see {@link http://php.net/preg_replace}
	* Results cached, so fill free invoke it several times without overhead of replace.
	*
	* @param int	$limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @return array Results of replace. Cached.
	**/
	public function replace($limit = -1){
		if (!$this->replaceValid){
			$this->replaceRes = \preg_replace($this->RegExp, $this->replaceTo, $this->sourceText, $limit);
			$this->replaceValid = true;
		}
		return $this->replaceRes;
	}
	/**
	* Split by regexp.
	*
	* @since Version 2.1.1
	*
	* @param int(-1)	$limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
	* @param int(0)	$flags {@link http://php.net/preg-split} for detailed descriptions of $flags.
	* @return &$this
	**/
	public function &split($limit = -1, $flags = 0){
		$this->matches = \preg_split($this->RegExp, $this->sourceText, $limit, $flags ?? 0);
		$this->matchesValid = true;
		return $this;
	}
}
