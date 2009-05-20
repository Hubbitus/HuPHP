<?
/**
* RegExp manupulation. PCRE-version.
*
* @package RegExp
* @version 2.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-02-11 13:41 ver 2.1 to 2.1.1
*	- Add method split
*
*	* 2009-03-18 17:09 ver 2.1.1 to 2.2
*	- In method ::convertOffsetToChars() many changes.
*		o Fix to walk through all members, not only 0 and 1
		o Recalculate offset may be done by many ways. See test/strlen_speed_tests.php for more detailes.
*			Short conclusion from this tests are:
*				1) It is very-very slowly operations, so
*					1.1) We refusal to do it in any time. This must be called
*						manually (and it also may need binary offset meantime too!!!).
*					1.2) For that, change access type to public
*				2) To case when it is needed second conclusion - the most fast way is mb_strlen, but it is not included in core PHP...
*					2.1) If available, mb_strlen is used
*					2.2) For capability, provide fallback to strlen(utf8_decode(...)) (2nd place of speed)
*		o Make default value for flag parameter ($flags = PREG_OFFSET_CAPTURE), as we have 1 implementation and only this flag have sence
**/

/*-inc
include_once('RegExp/RegExp_base.php');
*/
/**
* @uses RegExp_base
**/

class RegExp_pcre extends RegExp_base {
const className = 'RegExp_pcre';
/*
protected $sourceText;
protected $RegExp;

protected $matchCount;
protected $matches;
*/

/**
* {@inheritdoc}
**/
public function test(){
return ($this->matchCount = preg_match($this->RegExp, $this->sourceText));
}#m test

/**
* {@inheritdoc}
**/
public static function quote($toQuote, $delimeter = '/'){
	if (is_array($toQuote)){
	array_walk_recursive($toQuote, create_function('&$v,&$k,&$d', '$v = preg_quote($v, $d);'), $delimeter);
	return $toQuote;
	}
	else return preg_quote($toQuote, $delimeter);
}#m quote

/**
* {@inheritdoc}
**/
public function &doMatch($flags = null, $offset = null){
$this->matchCount = preg_match($this->RegExp, $this->sourceText, $this->matches, $flags, $offset);
$this->matchesValid = true;
//Now must be called manually, if needed! $this->convertOffsetToChars($flags);
return $this;
}#m doMatch

/**
* {@inheritdoc}
**/
public function &doMatchAll($flags = null, $offset = null){
$this->matchCount = preg_match_all($this->RegExp, $this->sourceText, $this->matches, $flags, $offset);
$this->matchesValid = true;
//Now must be called manually, if needed! $this->convertOffsetToChars($flags);
return $this;
}#m doMatchAll

/*
public static function &create($regexp = null, $text = null){
return new self($regexp, $text);
}#m create
Now automaticaly copy them from Single::create in base constructor
*/

/**
* Conversion bytes offsets to characters.
*
* Whith PREG_OFFSET_CAPTURE preg_match* returns bytes offset!!!! nor chars!!!!
* So, recalculate it in chars is several methods:
* 1) Using utf8_decode. See http://ru2.php.net/manual/ru/function.strlen.php
*	comment "chernyshevsky at hotmail dot com"
* 2) And using mb_strlen http://ru2.php.net/manual/ru/function.preg-match.php comment "chuckie"
*
* I using combination of its. And it independent of the presence mbstring extension!
*
* @param	int(PREG_OFFSET_CAPTURE)	$flags Flags which was used in previous operation.
* @return	nothing
*/
public final function convertOffsetToChars($flags = PREG_OFFSET_CAPTURE){
/*
* A recalculate offset may be done by many ways. See test/strlen_speed_tests.php for more detailes.
* Short conclusion from this tests are:
* 1) It is very-very slowly operations, so
*	1.1) We refusal to do it in any time. This must be called manually if you want (and it also may need binary offset meantime too!!!).
*	1.2) For that, change access type to public
* 2) To case when it is needed second conclusion - the most fast way is mb_strlen, but it is not included in core PHP...
*	2.1) If available, use mb_strlen
*	2.2) For capability, provide fallback to strlen(utf8_decode(...)) (2nd place of speed)
**/
	if ($this->matchCount and ($flags & PREG_OFFSET_CAPTURE)){
		if (function_exists('mb_strlen')){
		$func_strlen = create_function('$str', 'return mb_strlen($str, \'UTF-8\');');
		}
		else{//Fallback
		$func_strlen = create_function('$str', 'return strlen(utf8_decode($str));');
		}

		foreach($this->matches as &$match){
			foreach ($match as &$m){
			$m[1] = $func_strlen(substr($this->sourceText, 0, $m[1]));
			}
		}
	}
}#m convertOffsetToChars

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
	$this->replaceRes = preg_replace($this->RegExp, $this->replaceTo, $this->sourceText, $limit);
	$this->replaceValid = true;
	}
return $this->replaceRes;
}#m replace

/**
* Split by regexp.
*
* @since Version 2.1.1
*
* @param int(-1)	$limit If present - replace only $limit occurrences. In default case of -1 - replace ALL.
* @param int(null)	$flags {@link http://php.net/preg-split} for detailed descriptions of $flags.
* @return &$this
**/
public function &split($limit = -1, $flags = null){
$this->matches = preg_split($this->RegExp, $this->sourceText, $limit, $flags);
$this->matchesValid = true;
return $this;
}#m split
}#c RegExp_pcre
?>
