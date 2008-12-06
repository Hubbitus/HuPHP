<?
/**
* RegExp manupulation. PCRE-version.
* @package RegExp
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

include_once('RegExp/RegExp_base.php');

class RegExp_pcre extends RegExp_base {
const className = 'RegExp_pcre';
/*
protected $sourceText;
protected $RegExp;

protected $matchCount;
protected $matches;
*/

#Do test, faster then doMatch, don't filling ->matches, ->matchCount and other.
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

public function doMatch($flags = null, $offset = null){
$this->matchCount = preg_match($this->RegExp, $this->sourceText, $this->matches, $flags, $offset);
$this->matchesValid = true;
$this->convertOffsetToChars($flags);
return $this;
}#m doMatch

public function doMatchAll($flags = null, $offset = null){
$this->matchCount = preg_match_all($this->RegExp, $this->sourceText, $this->matches, $flags, $offset);
$this->matchesValid = true;
$this->convertOffsetToChars($flags);
return $this;
}#m doMatchAll

/*
public static function &create($regexp = null, $text = null){
return new self($regexp, $text);
}#m create
Now automaticaly copy them from Single::create in base constructor
*/

/**
* Whith PREG_OFFSET_CAPTURE preg_match* returns bytes offset!!!! nor chars!!!! 
* So, recalculate it in chars is several methods:
* 1) Using utf8_decode. See http://ru2.php.net/manual/ru/function.strlen.php
*	comment "chernyshevsky at hotmail dot com"
* 2) And using mb_strlen http://ru2.php.net/manual/ru/function.preg-match.php comment "chuckie"
*
* I using combination of its. And it independent of the presence mbstring extension!
*/
private final function convertOffsetToChars($flags){
	if ($this->matchCount and ($flags & PREG_OFFSET_CAPTURE) ){
		foreach($this->matches as &$m){
		$m[0][1] = strlen(utf8_decode(substr($this->sourceText, 0, $m[0][1])));
		$m[1][1] = strlen(utf8_decode(substr($this->sourceText, 0, $m[1][1])));
		}
	}
}#m convertOffsetToChars

/**
* Description see {@link http://php.net/preg_replace}
* @limit В случае, если параметр limit указан, будет произведена замена limit вхождений шаблона; в случае, если limit опущен либо равняется -1, будут заменены все вхождения шаблона. 
* @return &$this
**/
public function replace($limit = -1){
	if (!$this->replaceValid){
	$this->replaceRes = preg_replace($this->RegExp, $this->replaceTo, $this->sourceText, $limit);
	$this->replaceValid = true;
	}
return $this->replaceRes;
}#m replace

}#c RegExp_pcre
?>
