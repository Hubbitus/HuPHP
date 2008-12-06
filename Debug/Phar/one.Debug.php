<?
/** This is automaticaly generated file. Please, do not edit it! **/
?><?

/**
* Helper to more flexibility show large amount of data (long strings, dump of arrays etc.)
* @param string	$shortVar
* @param string	$longVar
* @return string
**/
function backtrace__printout_WEB_helper($shortVar, $longVar, $innerTagStart = '<textarea', $innerTagEnd='</textarea>'){
return '\'<span title="'.$longVar.'"
 onclick=\\\'this.bakonclick=this.onclick; this.onclick=null; var ttt = this.innerHTML; this.innerHTML="'.$innerTagStart.' style=\"color: green; width: 50em; height: 7em; overflow: auto\" ondblclick=\"this.parentNode.onclick=this.parentNode.bakonclick; var ttt=this.parentNode.title; this.parentNode.title=( this.defaultValue ? this.defaultValue : this.innerHTML); this.parentNode.innerHTML = ttt; \">" + this.title + "'.$innerTagEnd.'"; this.title = ttt;\\\'>'.$shortVar.'</span>\'';
}#f backtrace__printout_WEB_helper

/** For format description see {@link HuFormat class} **/
$GLOBALS['__CONFIG']['backtrace::printout'] = array(
	'FORMAT_WEB'	=> array(
		'A:::' => array(
			"<div style='text-align: left; font-family: monospace; padding-left:2em'>\n<b style='color: brown'>Backtrace:</b><br />",
			array(
				'I:::call' => array(
					'A:::' =>	array(
						'<b style=\'color: green\'>call:</b> ',
						array('sn:::class', '<span style=\'color: purple\'>', '</span>'),
						array('sn:::type', '<span style=\'color: brown\'>', '</span>'),
						array('sn:::function', '<span style=\'color: magenta\'>', '</span>'),
						array('E:::args', '\'(\'.$var->formatArgs().\')\''),
						"<br />\n",

						'&rArr;&rArr;<b style=\'color: red\'>file:</b> ',
						array('sn:::file', '<u>', '</u>'),
						' - ',
						array('sn:::line', '<b style=\'background-color: orange\'>', '</b>'),
						"<br />\n"
					),
				),

			),
			"</div>\n",
		),

		'argtypes'	=> array(
			'integer'	=> array('v:::'),//As is
			'double'	=> array('v:::'),
			
//			array('vn:::', '<textarea style="height:2.3em; width: 32em">', '</textarea>'),
//					   array('E:::', '\'\\\'\'.htmlspecialchars(substr($var, 0, 32)).((($sl = strlen($var)) < 32) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}\'');

/*
					array('E:::', '\'<span title="\'.$var.\'"
onclick=\\\'this.bakonclick=this.onclick; this.onclick=null; var ttt = this.innerHTML; this.innerHTML="<textarea onclick=\"return false;\" style=\"color: green; width: 50em; height: 7em\" ondblclick=\"this.parentNode.onclick=this.parentNode.bakonclick; var ttt=this.parentNode.title; this.parentNode.title=this.defaultValue; this.parentNode.innerHTML = ttt; \">" + this.title + "</textarea>"; this.title = ttt;\\\'>\\\'\'.htmlspecialchars(substr($var, 0, 32)).((($sl = strlen($var)) < 32) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}
 </span>\''),
*/
			'string'	=> array('E:::', backtrace__printout_WEB_helper('\\\'\'.htmlspecialchars(substr($var, 0, 32)).((($sl = strlen($var)) < 32) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}', '\'.htmlspecialchars($var).\'')),

//			'array'	=> array('E:::', '\'Array(\'.count($var).\')\''),
			'array'	=> array('E:::', backtrace__printout_WEB_helper('\'.\'Array(\'.sizeof($var).\')\'.\'', '\'.htmlspecialchars(dump::byOutType(OS::OUT_TYPE_BROWSER, $var, null, true)).\'', '<div style=\"display: table; border: thick dashed green; border-top: none\"', '</div>')),
			'object'	=> array('E:::', '\'Object(\'.get_class($var).\')\''),
			'resource'=> array('E:::', '\'Resource(\'.strstr($var, \'#\').\')\''),
			'boolean'	=> array('E:::', '$var ? \'True\' : \'False\''),
			'NULL'	=> 'Null',
			'default'	=> array('n:::', 'Unknown (', ')'),
		),
	),

	'FORMAT_CONSOLE'	=> array(
		'A:::' => array(
			"\033[1mBacktrace:\033[0m\n",
			array(
				'I:::call' => array(
					'A:::' =>	array(
						"\033[32mcall:\033[0m ",
						array('sn:::class', "\033[35m", "\033[0m"),
						array('sn:::type', "\033[33m", "\033[0m"),
						array('sn:::function', "\033[35;1m", "\033[0m"),
						array('E:::args', '\'(\'.$var->formatArgs().\')\''),
						"\n",

						"->\033[31;1mfile: \033[0m",
						array('sp:::file', '%s', '__vAr__'),
						':',
						array('sn:::line', "\033[43;1m", "\033[0m"),
						"\n",
					),
				),
			),
		),
	),
	'FORMAT_FILE'	=> array(
		'A:::' => array(
			"Backtrace:\n",
			array(
				'I:::call' => array(
					'A:::' =>	array(
						'call: ',
						array('s:::class'),
						array('s:::type'),
						array('s:::function'),
						array('E:::args', '\'(\'.$var->formatArgs().\')\''),
						"\n",

						'file: ',
						array('s:::file'),
						':',
						array('s:::line'),
						"\n",
					),
				),
			),
		),
	),
);
//$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']	= $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB'];
//$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']		= $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB'];
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']	= $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']	=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];
#Difference in argTypes
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['string']	= array('E:::', '\'\\\'\'.htmlspecialchars(substr($var, 0, 28)).((($sl = strlen($var)) < 28) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}\'');
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['array']	= array('E:::', '\'Array(\'.count($var).\')\'');

$GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_BROWSER]		=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB'];
$GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_CONSOLE]		=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']; 
$GLOBALS['__CONFIG']['backtrace::printout'][OS::OUT_TYPE_FILE]			=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE'];
?><?
/**
* FileSystem Exceptions
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/



class FilesystemException extends BaseException{
protected $fullPath = '';

	function __construct($message, $fullPath){
	$this->fullPath = $fullPath;
	parent::__construct($message);
	}

	// custom string representation of object
	public function __toString(){
	return __CLASS__ . ": [{$this->fullPath}]: {$this->message}\n";
	}
}

class RemoteGetException extends FilesystemException{}

class FileLoadErrorException extends FilesystemException{}
class FileNotReadableException extends FileLoadErrorException{}
class FileNotExistsException extends FileLoadErrorException{}

?><?
/**
* Base file operations.
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @CHANGELOG
*	- 2008-08-27
*	Added: clearPendingWrite(), __destructor(), appendString()
**/




class file_base{
private $filename = '';
private $rawFilename = '';	#Filename to try open. For error-reports.
private $dir = '';

private $content = '';
private $lineContent = null;
private $fd = null;

private $_lineSep = "\n";	#Unix by default

private $_linesOffsets = array();	#Cache For ->getLineByOffset and ->getOffsetByLine methods

protected $_writePending = false;

public function __construct($filename = ''){
$this->setPath($filename);
}#__c

#Write all pendings write
public function __destruct(){
	if ($this->_writePending) $this->writeContents();
}

public function &setPath($filename){
	if ($filename){
	$this->rawFilename = $filename;
	}
return $this;
}#m setPath

public function getLineSep() { return $this->_lineSep; }#m getLineSep
public function setLineSep($newSep) {
$this->_leneSep = $newSep;
$this->_linesOffsets = array();
}#m getLineSep

public function open($mode, $use_include_path = false , $zcontext = null){
$this->checkOpenError(
	(bool)
		($zcontext
		?
		($this->fd = fopen($this->path(), $mode, $use_include_path, $zcontext))
		:
		($this->fd = fopen($this->path(), $mode, $use_include_path))
		)
);
$this->lineContent = array();
$this->content = '';
}#m open

public function getline($length = null){
return $length ? fgets(REQUIRED_VAR($this->fd), $length) : fgets(REQUIRED_VAR($this->fd));
}#m getline

public function getLineAt($line){
	if (!$this->lineContent) $this->explodeLines($updateLineSep);
return $this->lineContent[$line];
}#m getLineAt

public function getTail ($maxlength = -1, $offset = 0){
return stream_get_contents($this->fd, $maxlength, $offset);
}#m getTail

/**
* @Deprecated !
* Use loadContentInstead. Lines exploding automatic now in any case if
* you get cces "by lines" methods.
*
* This is because file() reads file in array, but each line include line separator,
* but it not needed! See implementation of ->getBLOB() for example
**/
public function loadByLines($use_include_path = false, $resource_context = null){
return $this->loadContent($use_include_path, $resource_context);
/*
$this->checkOpenError(
	(bool)($this->lineContent = @file($this->getPath(), $use_include_path, $resource_context))
);
$this->_linesOffsets = array();
*/
}#m loadByLines

public function loadContent($use_include_path = false, $resource_context = null, $offset = null, $maxlen = null){
$this->checkOpenError(
	(bool)
		($maxlen
		?
		($this->content = file_get_contents($this->path(), $use_include_path, $resource_context, $offset, $maxlen))
		:
		($this->content = file_get_contents($this->path(), $use_include_path, $resource_context, $offset))
		)
);
$this->lineContent = array();
$this->_linesOffsets = array();
}#m loadContent

public function isExists(){
#ЧуднО = file_exists ('') === true!!!
return ('' != $this->path() and file_exists($this->path()));
}#m isExists

public function isReadable(){return is_readable($this->path());}

public function getDir(){return dirname($this->filename);}
public function path(){
//return $this->getDir().DIRECTORY_SEPARATOR.$this->path();
return $this->rawFilename;
}

/**
* Return array of lines
* @param array $lines. If empty array - whole array of lines. Else Array(int $offset  [, int $length  [, bool $preserve_keys  ]] ). See http://php.net/array_slice
* @param boolean(true) $updateLineSep. See explanation in ->explodeLines() method.
* @Throw(VariableEmptyException)
* @return array Array of lines
**/
public function getLines(array $lines = array(), $updateLineSep = true){
$this->checkLoad();
	if (!$this->lineContent) $this->explodeLines($updateLineSep);

	if(!empty($lines)) return call_user_func_array('array_slice', array_merge(array( 0 => $this->lineContent), $lines) );
	else return $this->lineContent;
}#m getLines

/**
* Explode whole contents by lines.
* @param boolean $updateLineSep if true - update lineSep by presented in whole content.
**/
protected function explodeLines($updateLineSep = true){
preg_match_all('/(.*?)([\n\r])/', $this->content, $this->lineContent, PREG_PATTERN_ORDER);
	if ($updateLineSep) $this->_lineSep = $this->lineContent[2][0/*Any realy. Assuming all equal.*/];
$this->lineContent = $this->lineContent[1];
$this->_linesOffsets = array();
}#m explodeLines

/**
* Implode lineContent to whole contents.
* @param string	$implodeWith String implode with. If null, by default - ->_lineSep.
* @param boolean	$updateLineSep if true - update lineSep by presented $implodeWith.
**/
protected function implodeLines($implodeWith = null, $updateLineSep = true){
	if ($implodeWith and $updateLineSep) $this->setLineSep($implodeWith);
$this->_linesOffsets = array();
return ($this->content = implode($this->getLineSep(), $this->lineContent));
}#m implodeLines

/**
* Return string of content
* @param string	$implodeWith See descr ->implodeLines()
* @param boolean	$updateLineSep See descr ->implodeLines()
* @return string
**/
public function getBLOB($implodeWith = null, $updateLineSep = true){
	if (
		! $this->content
		or
		( $implodeWith and $implodeWith != $this->_lineSep)
	)
	$this->implodeLines($implodeWith = null, $updateLineSep = true);
return $this->content;
}#m getBLOB

/**
* Clear pending writes.
*
* @return &$this
**/
public function &clearPendingWrite(){
$this->_writePending = false;
return $this;
}#m clearPendingWrite

/**
* @param string	$string. String to set from.
* @return &$this
* @Throw(VariableRequiredException)
**/
public function &setContentFromString($string){
$this->content = REQUIRED_VAR($string);
$this->lineContent = array();
$this->_linesOffsets = array();
$this->_writePending = true;
return $this;
}#m setContentFromString

/**
* Append string to pending write buffer.
* @param string	$string. String to append from.
* @return &$this
* @Throw(VariableRequiredException)
**/
public function &appendString($string){
return $this->setContentFromString($this->content . REQUIRED_VAR($string));
}#m appendString

/**
* Writes whole contents to file (filename may be set via ->setPath('NewFileName'))
* @param integer flags See http://php.net/file_put_contents
* @param resource $resource_context See http://php.net/file_put_contents
* @param string	$implodeWith See descr ->implodeLines()
* @param boolean	$updateLineSep See descr ->implodeLines()
* @return integer Count of written bytes
**/
public function writeContents($flags = null, $resource_context = null, $implodeWith = null, $updateLineSep = true){
$this->checkOpenError(
	#$this->rawFilename because may be file generally not exists!
	(bool) ($count = @file_put_contents($this->path(), $this->getBLOB($implodeWith, $updateLineSep), $flags, $resource_context))
);
return $count;
}#m writeContent

/**
* Calculate lie number by offset. Line-separators on end of string.
* @param integer $offset
* @return integer
* @Throw(VariableRangeException)
**/
public function getLineByOffset($offset){
	if (!$this->_linesOffsets) $this->makeCacheLineOffsets();
	if ($offset > $this->_linesOffsets[sizeof($this->_linesOffsets)-1][1]) throw new VariableRangeException('Overflow! This offset does not exists.');

#Data ordered - provide binary search as alternative to array_search
$size = sizeof($this->_linesOffsets) - 1;	#For speed up only
$left = 0; $right = $size;	#Points of interval
$found = false;
$line = ceil($size / 2);
	/*
	Boundary conditions. Additional check of lowest value is mandatory, if used ceil() (0 is not accessible).
	Additional check of highest value addad only to efficient
	adjusting, because on it point the maximum time for the
	convergence of the algorithm
	*/
	if ($offset >= $this->_linesOffsets[0][0] and $offset <= $this->_linesOffsets[0][1])
	return 0;

	if ($offset >= $this->_linesOffsets[$size][0] and $offset <= $this->_linesOffsets[$size][1])
	return $size;

	do{
//c_dump($line, '$line');
//c_dump($prevLine, '$prevLine');
		if ( $offset >= $this->_linesOffsets[$line][0] ){
			if ( $offset <= $this->_linesOffsets[$line][1] ){
			$found = true;	#Done
			}
			else{
			$left = $line;
			$line += ceil( ($right - $line) / 2 );
			}
		}
		else{
		$right = $line;
		$line -= ceil( ($line - $left) / 2);
		}
	} while(!$found);

	if ($found === true) return $line;
	else return false;
}#m getLineByOffset

/**
* Oppositive ->getLineByOffset.
* @param integer $line
* @return array(OffsetBeginLine, OffsetEndLine). In OffsetEndLine included length of ->_lineSep!
**/
public function getOffsetByLine($line){
	if (!$this->_linesOffsets) $this->makeCacheLineOffsets();
	if ($line >= sizeof($this->_linesOffsets)) throw new VariableRangeException('Overflow! This line does not exists.');

return $this->_linesOffsets[$line];
}#m getOffsetByLine

############################################
#####private functions
############################################
private function checkOpenError($succ){
	if ( ! $succ ){
		if (!$this->isExists()) throw new FileNotExistsException('File not found', $this->path());
		if (!$this->isReadable()) throw new FileNotReadableException('File not readable. Check permissions.', $this->path());
		throw new FileNotReadableException('Unknown error get file.', $this->path());
	}
}#m checkOpenError

private function checkLoad(){
	if (empty($this->lineContent) and empty($this->content)) throw VariableEmptyException ('Line-Content and Contentis empty! May be you forgot call one of ->load*() method first?');
}#m checkLoad

private function makeCacheLineOffsets(){
$this->_linesOffsets = array();
$offset = 0;
$lines =& $this->getLines();

$linesCount = sizeof($lines);	#For speed up
#First line is additional case
$this->_linesOffsets[0] = array($offset, ($offset += -1 + strlen(utf8_decode($lines[0])) + strlen(utf8_decode($this->getLineSep()))) );
	#From 1 line, NOT 0
	for($i = 1; $i < $linesCount; $i++){
	$this->_linesOffsets[$i] = array($offset + 1, ( $offset += strlen(utf8_decode($lines[$i])) + strlen(utf8_decode($this->getLineSep())) )  );
	}

//c_dump($this->_linesOffsets, '$this->_linesOffsets');
}#m makeCacheLineOffsets

}#c file_base
?><?
/**
* RegExp manupulation.
* @package RegExp
* @version 2.1b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-29
*	- Separate classes RegExp_base_base and RegExp_base to allov using this on PHP < 5.3.0-dev
*	- Add doc to methods, reformatting.
* 2008-05-30 19:05
*	- Made $paireddelimeters method NOT static. It is allowed in implementation, because it is may
*		now be used as property. So, if outsource code use it static - must change it. This is sacrifice
*		to to compatibility with PHP < 5.3.0 (whithout late static bindings)
*	- getMatch add in eval-code, to avoid fatal errors in earler versions PHP
**/




	#To method "create"

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
	* Aka __construct, but for static call. Primarly needed to create object
	* of future defined class in base (see getMatch method)
	* Derived from HuClass::create
	* @method create()
	* @return Object(RegExp_base)
	**/

	/**
	* Constructor.
	* For parameters {@see ->set()}
	**/
	public function __construct($regexp = null, $text = null, $replaceTo = null){
	$this->set($regexp, $text, $replaceTo);
	}#_c

	/**
	* Return N-th single match
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
	**/
	abstract public function doMatch($flags = null, $offset = null);#{}# MUST return $this;

	/**
	* @see ->doMatch(). But match all occurences.
	**/
	abstract public function doMatchAll($flags = null, $offset = null);#{}# MUST return $this;

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
	* @param integer $item. If not null - pount to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return char
	**/
	public function getRegExpDelimiterEnd($item = null){
		if (isset($this->paireddelimeters[$this->getRegExpDelimiterStart($item)]))
		return $this->paireddelimeters[$this->getRegExpDelimiterStart($item)];
		else return $this->getRegExpDelimiterStart($item);
	}#m getRegExpDelimiterEnd

	/**
	* Assume RegeExp correct. Do not check it.
	* @param integer $item. If not null - pount to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
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
	* @param integer $item. If not null - pount to item in array of RegExps, ONLY IF it is array. If null - 0 element assumed.
	* @return char
	**/
	public function getRegExpModifiers($item = null){
	$item = is_null($item) ? 0 : $item;
		if (is_array($this->RegExp)) return (string)substr($this->RegExp[$item], strrpos($this->RegExp[$item], $this->getRegExpDelimiterEnd($item)) + 1 );
		else return (string)substr($this->RegExp, strrpos($this->RegExp, $this->getRegExpDelimiterEnd()) + 1 );
	}#m getRegExpModifiers

	/**
	* Description see {@link http://php.net/preg_replace}
	* @limit В случае, если параметр limit указан, будет произведена замена limit вхождений шаблона; в случае, если limit опущен либо равняется -1, будут заменены все вхождения шаблона. 
	* @return mixed	Replaced value.
	**/
	abstract public function replace($limit = -1);

	/**
	* Quote given string or each (recursive) string in array.
	* @param	string|array	$toQuote
	* @param	string='/'	$delimiter. Chars to addition escape. Usaly (and default) char start and end of regexp.
	* @return	string|arrya	Same type as given.
	**/
	abstract public static function quote($toQuote, $delimeter = '/');

	/**
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
	eval ( <<< HEREDOC
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
			//Additionaly new static::className($regexp, $text); DON'T work, so using one more variable
			//$tmpR = new static::className($regexp, $text);
			$className = static::className;
			$tmpR = new $className($regexp, $text);
			$tmpR->doMatch();
			return $tmpR->match($N);
			}#m getMatch
		}
HEREDOC
	);
	}
	else{
		abstract class RegExp_base extends RegExp_base_base{
			public static function getMatch($regexp, $text, $N=0){
			throw new ClassMethodExistsException ('RegExp_base::getMatch not implemented for this version of PHP!');
			}
		}
	}
?><?
/**
* RegExp manupulation. PCRE-version.
* @package RegExp
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/



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
<?
/**
* Singleton pattern.
*
* @package Vars
* @subpackage Classes
* @version 1.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	2008-05-30 13:22
*	- Fore bakward capability replace construction (!@var ?: "Error") to (!@var ? '' : "Error")
**/


# For OS::is_includeable()

/**
* Example from http://ru2.php.net/manual/ru/language.oop5.patterns.php
* Modified
**/
class Single{
// Hold an instance of the class
private static $instance = array();

	/**
	* A private constructor; prevents direct creation of object
	**/
	protected final function __construct(){
	echo 'I am constructed. But can\'t be :) ';
	}//__c

	/**
	* The main singleton ststic method
	* All call must be: Single::singleton('ClassName'). Or by its short alias: Single::def('ClassName')
	* @param	string	$className Class name to provide Singleton instance for it.
	* @params variable number of parameters. Any other parameters directly passed to instantiated class-constructor.
	**/
	public static function singleton($className){
		if (!isset(self::$instance[$className])){// @TODO: provide hashing class name and args, and index by hash.
		self::tryIncludeByClassName($className);

		$args = func_get_args();
		unset($args[0]);
//		self::$instance[$className] = new $className($args);
//		self::$instance[$className] = new $className(@$args[1]);

		/*
		Using Reflection to instanciate class with any args.
		See http://ru2.php.net/manual/ru/function.call-user-func-array.php, comment of richard_harrison at rjharrison dot org
		*/
		// make a reflection object
		$reflectionObj = new ReflectionClass($className);
		// use Reflection to create a new instance, using the $args
		self::$instance[$className] = $reflectionObj->newInstanceArgs($args);
		}

	return self::$instance[$className];
	}#m singleton

	/**
	* The default configured. Short alias for {@see ::singleton()}
	**/
	public static function def($className){
	return self::singleton($className, $GLOBALS['__CONFIG'][$className]);
	}#m def

	/**
	* Description
	* @param string	$className Name of needed class
	* @return
	**/
	public static function tryIncludeByClassName($className){
		#is_readable is not use include_path, so can not use this check. More explanation see {$link OS::is_includeable()}
		if (!class_exists($className) and isset($GLOBALS['__CONFIG'][$className]['class_file'])) OS::is_includeable($GLOBALS['__CONFIG'][$className]['class_file'], true);

		#repetition check
		if (!class_exists($className)) throw new ClassNotExistsException($className . ' NOT exist!'. (!@$GLOBALS['__CONFIG'][$className]['class_file'] ?'': 'And, additionaly include provided path ['.$GLOBALS['__CONFIG'][$className]['class_file'].'] not helped in this!'));
	}#m tryIncludeByClassName

	/**
	* Prevent users to clone the instance
	**/
	public function __clone(){
	trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
}#c Single

// This will always retrieve a single instance of the class
//$test = Single::singleton();
//$test->bark();
//$test = Single::singleton()->bark();
?><?
/**
 * System environment and information
 * @package System ??
 * @version 2.0b
 * @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
 * @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
 */

/**
* Class OS has mainly (all) static methods, to determine system-enveroments, like OS or type of out.
* Was System, but it is registered in PEAR, change to OS
*/
class OS {
const OUT_TYPE_BROWSER = 1;
const OUT_TYPE_CONSOLE = 2;
const OUT_TYPE_PRINT = 4; /** Pseudo!!! Need automaticaly detect OUT_TYPE_BROWSER or OUT_TYPE_CONSOLE */
const OUT_TYPE_FILE = 8;
const OUT_TYPE_WAP = 16;
#const OUT_TYPE_ = 16;

/**
* Possible return-values of 
* http://ru2.php.net/php_sapi_name comment from "cheezy at lumumba dot luc dot ac dot be"
*/
static $SAPIs = array(
	'aolserver',
	'activescript',
	'apache',
	'cgi-fcgi',
	'cgi',
	'isapi',
	'nsapi',
	'phttpd',
	'roxen',
	'java_servlet',
	'thttpd',
	'pi3web',
	'apache2filter',
	'caudium',
	'apache2handler',
	'tux',
	'webjames',
	'cli',
	'embed,',
	'milter'
);


	/**
	* Determines out type of current-running process.
	* @return Now one of const: ::OUT_TYPE_BROWSER or ::OUT_TYPE_CONSOLE
	*/
	static public function getOutType(){
		if (isset($_SERVER['HTTP_USER_AGENT'])) return self::OUT_TYPE_BROWSER;
		else return self::OUT_TYPE_CONSOLE;
	}#m getOutType

	/**
	* php_sapi_name()
	* @return
	*/
	static public function phpSapiName(){
	return php_sapi_name();
	}#m phpSapiName

	/**
	* Check if file is includable. I can't just use if (@inlude($file)). Or, more exactly i can, but
	*	it is have small different meaning:
	*	@include('include.php') not return and NOT shown errors in including file! Nothing:
	*		Not Notice, Warning or Fatal!!!!
	*		See http://ru2.php.net/manual/ru/function.include-once.php comments of
	*		"flobee at gmail dot com" and "php at metagg dot com" and http://php.net/include/
	*		comment of "medhefgo at googlemail dot com"
	*		In other words, absent way (get me known if I am wrong) to suppress errors like
	*		'file not found' or 'not readable', construction @include suppres ALL (even Critical!)
	*		in including files, and nested (included from including).
	*	Result of check may be also applyable to require()
	* @param	string $filenam As it can be passed to include or require.
	* @param	bool(false)	$include If result is true, include it!
	* @return
	*/
	static public function is_includeable($filename, $include = false){
		/** is_file, is_readable not suitable, because include_path do not take effect.
		* And opposite comment of "php at metagg dot com" and "medhefgo at googlemail dot com",
		* woudn't manualy check all paths in include_path. Just open this file to read
		* with include_path check parameter support! */
		if ($res = @fopen($filename, 'r', true)){
		fclose($res);	// Not realy need opened file, only result of opening.
			if ($include) include($filename);
		}
	return (bool)$res;
	}#m is_inludeable
}#c OS
?><?
/**
* Toolkit of small functions as "macroses".
* DEBUG version
* @package Macroses
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	2008-05-29 19:55 version 1.2 from 1.0
*	- Rewritten with VariableRequiredException. Now provide only backtrace, not Tokenizer! It takes
*	less overhead and debag/nodebug handled in one place (in exception class)
**/



/**
* Thows {@see VariableRequiredException) if is_null($var)
* In constructor of VariableIsNullException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is usefull in direct operations like assigment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialise them arg of Tokenizer, else real parse.
* @return &mixed
* @Throw(VariableIsNullException)
**/
function &REQUIRED_NOT_NULL(&$var, $varname = null){
	if (is_null($var)){
		throw new VariableIsNullException(
			new backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
?><?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Classes
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-31 5:31 v 1.0b to 1.1
*	- Add static method ::createWithoutLSB.
* 2008-06-05 16:00
*	- In function classCREATE provide all aditions arguments to HuClass::createWithoutLSB
**/



/**
* To explicit indicate what value not provided, also Null NOT provided too!
**/
class NullClass {}

abstract class HuClass{
	/**
	* To extends most (all) classes.
	* Or to fast copy (with runkit_method_copy) into other classes.
	* Method to allow constructions like: className::create()->methodName() because (new classname())->methodName are NOT allow them!!!
	* @param variable parameters according to class.
	* @return instance of the reguired new class.
	**/
	static function create(){
//	$reflectionObj = new ReflectionClass(static::className);
	#http://blog.felho.hu/what-is-new-in-php-53-part-2-late-static-binding.html
	$reflectionObj = new ReflectionClass(get_called_class());

		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(func_get_args());
		else return $reflectionObj->newInstance();
	}#m create

	/**
	* This is similar create, but created for backward capability only.
	* It is UGLY. Do not use ti, if you have choice.
	* It is DEPRECATED immediately after creation! But now, realy, it is stil neded :(
	*
	* @deprecated
	* @param $directClassName = null - The directy provided class name to instantiate.
	*	If not provided, as last chance, try get_called_class, after throw exception 
	* @params variable parameters according to class.
	* @return instance of the reguired new class.
	* @Throw(ClassUnknownException)
	**/
	static function createWithoutLSB($directClassName = null /*, Other Params */){
		if (function_exists('get_called_class')){
		$reflectionObj = new ReflectionClass(get_called_class());
		}
		elseif($directClassName){
		$reflectionObj = new ReflectionClass($directClassName);
		}
		else{
		throw new ClassUnknownException('You not provide ClassName, and Late State Binding (LSB) is not available on your system (present PHP 5.3.0-dev). Do not known what class need be instanciated. Sory! ');
		}

	$args = func_get_args();//0 argument - $directClassName
		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(array_slice($args, 1));
		else return $reflectionObj->newInstance();
	}#m createWhithoutLSB
}#c HuClass

/**
* Free function. For instantiate all objects.
* {@inheritdoc HuClass::createWithoutLSB}
**/
function classCREATE($ClassName = null /*, Other Params */){
$args = func_get_args();//0 argument - $directClassName
return call_user_func_array(
	array(
		'HuClass',
		'createWithoutLSB'
	),
	array_slice($args, 1)
);
}
?><?
/**
* Toolkit of small functions as "macroses".
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Return first NON-empty string if present. Silent return empty str "" otherwise.
* @params	variable amount of arguments.
* @return string
**/
function EMPTY_STR(){
$numargs = func_num_args();
$i=0;
	while (
		$i < $numargs
		 and
		!(string)($res = func_get_arg($i++))
	){/*Nothing doing, just skip it */}
return (string)$res;
}

#Если НЕпустой первый аргумент, то вернуть его c префиксом и суффиксом. Если пустой - дефолтное значение
function NON_EMPTY_STR (&$str, $prefix='', $suffix='', $defValue=''){
return ( @$str ? (string)$prefix.$str.$suffix : $defValue);
}
?><?
/**
* Provide easy to use settigns-cllass for many purpose. Similar array
* of settings, but provide several addition methods, and magick methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see
* class HuFormat}).
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-30 23:19
*	- Move include macroses REQUIRED_VAR.php and REQUIRED_NOT_NULL.php after declaration class
*	 settings to break cycle of includes
**/


	#Static method ::create()

class settings extends HuClass{
protected $__SETS = array();#Сами настройки, массив

	/**
	* Constructor.
	* @param array=null $array
	**/
	function __construct(array $array = null){
		if ($array) $this->mergeSettingsArray($array);
	}#constructor

	public function setSetting($name, $value){
	$this->__SETS[$name] = $value;
	}

	#ПЕРЕЗАПИСЫВАЕТ ВСЕ настройки. Для изменения отдельных - setSetting
	#Хорошо было бы это все в setSettings запихать, но перегрузка не поддерживается :(. Что ж, будут разные именаю
	public function setSettingsArray(array $setArr){
	$this->__SETS = REQUIRED_VAR($setArr);
	}

	#ПЕРЕЗАПИСЫВАЕТ УКАЗАННЫЕ настройки. Для изменения отдельных - setSetting
	#Хорошо было бы это все в setSettings запихать, но перегрузка не поддерживается :(. Что ж, будут разные именаю
	public function mergeSettingsArray(array $setArr){
	$this->__SETS = array_merge((array)$this->__SETS, REQUIRED_VAR($setArr));
	}

	public function getProperty($name){
	return ($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}

	function __get($name){
	return $this->getProperty($name);
	}

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	* @param	string	$name	Name of required property
	* @return boolean
	*/
	public function __isset($name) {
	return isset($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}#m __isset

	/**
	* Возвращает строку, в которую объединены требуемые (по представленному порядку) настройки.
	* Descriptiopn of elements $fields {@see ::formatField}
	**/
	public function getString(array $fields){
	$str = '';
		foreach (REQUIRED_VAR($fields) as $field){
		$str .= $this->formatField($field);
		}
	return $str;
	}#m getString

	/**
	* Format Field primarly for {@see ::getString}, but may be used and separatly
	* $field one of:
	*	1) Именем настройки. Если найдена такая настройка и она не пуста, подставляется она
	*	2) Просто константной строкой, тогда выводится как есть
	*	2) Массивом, формата:
	*		array(
	*		'str' => Имя настройки. (обязательно)
	*		'prefix' => ''
	*		'suffix' => ''
	*		'defValue' => ''
	*		)
	*		Вместо ассоциативного массива, допустимы и числовые стандартные индексы, чтобы короче писать не:
	*		array('str' =>'tag', 'prefix' => '<', 'suffix' => '>', 'defValue' => '<unknown>'),
	*		а просто, коротко и красиво
	*		array('tag', '<', '>', '<unknown>'),
	*		Передаются в макрос NON_EMPTY_STR, см. его для подробностей
	* @return string
	**/
	public function formatField($field){
		if (is_array($field)){
			if (!isset($field[0])) $field = array_values($field);
		return NON_EMPTY_STR(@$this->getProperty($field[0]), @$field[1], @$field[2], @$field[3]);
		}
		else{
		return EMPTY_STR(@$this->getProperty($field), $field);#Или по имени настройку, если это просто текст;
		}
	}#m formatField

	/**
	* Clear all settings
	* @return $this
	*/
	public function clear(){
	$this->__SETS = array();
	}#m clear

	/**
	* Number of settings.
	* @return integer
	**/
	public function length(){
	return sizeof($this->__SETS);
	}#m length
	
}#c settings

/**
* It's Before declaration of VariableRequiredException may produce cycle of includes...
**/



#Для удобного наследования
class get_settings{
//НЕ забыть его где-то инициализировать!!!
protected /* settings */ $_sets = null;

	public function &__get ($name){#Переопределяем, чтобы сделать ссылку на настройки не изменяемой!
	#таким образом настройки менять можно будет, а сменить объект настроек - нет
		if ('settings' == $name) return $this->_sets;
	}#__get

	/**
	* Return settings
	* @return	&Object(settings)
	*/
	public function &sets(){
	return $this->_sets;
	}#m sets
}#c get_settings<?
/**
* Debug and backtrace toolkit.
* @package Debug
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-31 03:19
*	- Add capability to PHP < 5.3.0-dev:
*		* Replace construction ($var ?: "text") with macros EMPTY_STR
* 2008-05-25 17:26
*	- Change 
**/







class HuError_settings extends settings{
#Defaults
protected $__SETS = array(
	/**
	* @example HuLOG.php
	*/
	'FORMAT_WEB'		=> array(),	/** For strToWeb().		If empty (by default): dump::w */
	'FORMAT_CONSOLE'	=> array(),	/** For strToConsole().	If empty (by default): dump::c */
	'FORMAT_FILE'		=> array(),	/** For strToFile().	If empty (by default): dump::log */

	/**
	* @see ::updateDate()
	**/
	'AUTO_DATE'		=> true,
	'DATE_FORMAT'		=> 'Y-m-d H:i:s',
);

/**
* @example
* protected $__SETS = array(
*	#В формате settings::getString(array)
*	'FORMAT_CONSOLE'	=> array(
*			array('date', "\033[36m", "\033[0m"),
*		'level',
*			array('type', "\033[1m", "\033[0m: ", ''),//Bold
*		'logText',
*			array('extra', "\n"),
*		"\n"
*		),
*	'FORMAT_WEB'	=> array(
*			array('date', "<b>", "</b>"),
*		'level',
*			array('type', "<b>", "</b>: ", ''),
*		'logText',
*			array('extra', "<br\\>\n"),
*		"<br\\>\n"
*		),
*	'FORMAT_FILE'	=> array(
*		'date',
*		'level',
*			array('type', '', ': ', ''),
*		'logText',
*			array('extra', "\n"),
*		"\n"
*		),
*	),
*	);
**/
}#c HuError_settings

class HuError extends settings{
/** Self settings. **/
protected /* settings */ $_sets = null;
protected $_curTypeOut = OS::OUT_TYPE_BROWSER;

	public function __construct(HuError_settings $sets = null){
	$this->_sets = EMPTY_VAR($sets, new HuError_settings);
	}#m __construct

	/**
	* Due to absent mutiple inheritance in PHP, just copy/pasted from class get_settings.
	* Переопределяем, чтобы сделать ссылку на настройки не изменяемой!
	* таким образом настройки менять можно будет, а сменить объект настроек - нет
	* @param string Needed name
	* @return mixed Object of settings.
	**/
	function &__get ($name){
		switch ($name){
		case 'settings': return $this->_sets;
		break;
		case 'date':
		case 'DATE':
			if (!@$this->getProperty($name)) $this->updateDate();
		//break;	/** NOT need break. Create by read, and continue return value!

		default:
		/**
		* Set properties is implicit and NOT returned reference by default.
		* But for 'settings' we want opposite reference. Whithout capability of functions
		* overload by type arguments - is only way silently ignore Notice: Only variable references should be returned by reference
		**/
		$t = $this->getProperty($name);
		return $t;
		}
	}#__get

	/**
	* String to print into file.
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_FILE setting used.
	*	And if it settings empty (or not exists) too, just using dump::log() for all filled fields.
	* @return string
	**/
	public function strToFile($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_FILE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_FILE)) return $this->getString($format);
		else return dump::log($this->__SETS, null, true);
	}#m strToFile

	/**
	* String to print into user browser.
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_WEB setting used.
	*	And if it settings empty (or not exists) too, just using dump::w() for all filled fields.
	* @return string
	**/
	public function strToWeb($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_BROWSER;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_WEB)) return $this->getString($format);
		else return dump::w($this->__SETS, null, true);
	}#m strToWeb

	/**
	* String to print into user brawser.
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_CONSOLE setting used.
	*	And if it settings empty (or not exists) too, just using dump::c() for all filled fields.
	* @return string
	**/
	public function strToConsole($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_CONSOLE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_CONSOLE)) return $this->getString($format);
		else return dump::c($this->__SETS, null, true);
	}#m strToConsole

	/**
	* String to print. Automaticaly detect Web or Console. Detect by {@link OS::getOutType()}
	*	and invoke appropriate ::strToWeb() or ::strToConsole()
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_PRINT;//Pseudo. Will be clarified.
		if (OS::OUT_TYPE_BROWSER == OS::getOutType()) return $this->strToWeb($format);
		else return $this->strToConsole($format, null, true);
	}#m strToPrint

	/**
	* Convert to string by type.
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = ''){
	$this->_curTypeOut = $type;
		switch ($type){
		case OS::OUT_TYPE_BROWSER:
		return $this->strToWeb($format);
		break;

		case OS::OUT_TYPE_CONSOLE:
		return $this->strToConsole($format);
		break;

		case OS::OUT_TYPE_FILE:
		return $this->strToFile($format);
		break;

		#Addition
		case OS::OUT_TYPE_PRINT:
		return $this->strToPrint($format);
		break;

		default:
		throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m strByOutType

	/**
	* On echo and print was detect, and provide correct form
	* @return string ::strToPrint()
	**/
	public function __toString(){
	return $this->strToPrint();
	}#m __toString

	/**
	* Overload settings::setSetting() to handle autodate
	* @inheritdoc
	**/
	public function setSetting($name, $value){
	parent::setSetting($name, $value);

	$this->updateDate();
	}#m setSetting

	/**
	* Overload settings::setSettingsArray() to handle autodate
	* @inheritdoc
	* @return $this
	**/
	public function setSettingsArray(array $setArr){
	parent::setSettingsArray($setArr);

	#Insert after update data
	$this->updateDate();
	return $this;
	}#m setSettingsArray

	/**
	* Just alias for ::setSettingsArray()
	* @param	$setArr
	* @return mixed	::setSettingsArray()
	**/
	public function setFromArray(array $setArr){
	return $this->setSettingsArray($setArr);
	}#m setFromArray

	/**
	* Overload settings::mergeSettingsArray() to handle autodate
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
	#Insert BEFORE update data in merge. User data 'date' must overwrite auto, if present!
	$this->updateDate();

	parent::mergeSettingsArray($setArr);
	}#m mergeSettingsArray

	/**
	* Just alias for ::mergeSettingsArray()
	* @param	$setArr
	* @return mixed	::mergeSettingsArray()
	**/
	public function mergeFromArray(array $setArr){
	$this->mergeSettingsArray($setArr);
	}#m mergeFromArray

	/** If settings->AUTO_DATE == true and settings->DATE_FORMAT correctly provided - update current
	* date on ->date
	* @param
	* @return
	**/
	public function updateDate(){
		if (
			$this->settings->AUTO_DATE
			and
			/** Parent::setSetting instead $this-> to aviod infinity recursion */
			$this->settings->DATE_FORMAT) parent::setSetting('date', date($this->settings->DATE_FORMAT));
	}#m updateDate

	/**
	* Overloading getString to separetly handle 'extra'
	* @inheritdocs	
	**/
	public function formatField($field){
		if (is_array($field)){
			 if(!isset($field[0])) $field = array_values($field);
		$fieldValue = @$this->{$field[0]};
		}
		else{ 
		$field = (array)$field;
		$fieldValue = EMPTY_VAR(@$this->{$field[0]}, $field[0]); //Setting by name, or it is just text
		}

		if ($fieldValue instanceof HuError){
		return NON_EMPTY_STR($fieldValue->strByOutType($this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		elseif($fieldValue instanceof backtrace){
		return NON_EMPTY_STR($fieldValue->printout(true, null, $this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		else return NON_EMPTY_STR($fieldValue, @$field[1], @$field[2], @$field[3]);
	}#m formatField
}#c HuError

/**
* To allow out any data
**/
class ExtraData extends HuError{
	/**
	* Constructor
	* @param mixed	$data
	**/
	public function __construct($data){
	$this->__SETS = $data;
	}#__c
}#c ExtraData
?><?
/**
* Debug and backtrace toolkit.
* @package Debug
* @subpackage HuFormat
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/












class HuFormatException extends VariableException{}

class HuFormat extends HuError{
	/** Replace this in ->_format on real value of _value (after process mod_s) **/
	const sprintf_var = '__vAr__';
	/** Var to process in eval-string in mod_e. In eval string off course witch sign $. **/
	const evalute_var = 'var';

	/** Separator to separate mods from name in one string. For more info see {@see ::parseModsName()} **/
	const mods_separator = ':::';

	/**
	* For each present Mod we must have method with name "mod_[mod]" where [mod] is letter of mode.
	*	Additionally, because PHP function (methods too) name are case insensitive, for upper-case letter
	*  modifiers must used double letters.
	* 	For example:
	* 		Mod 'e' => mod_e
	* 	 	Mod 'e' => mod_EE (same as mod_ee)
	* @var array
	**/
	static public $MODS = array(
	'A'	=> 1,	#ALL. Exclusive, all other modifiers not processed. Each process as HuFormat.
	's'	=> 2,	#Setting
	'a'	=> 4,	#Array
	'n'	=> 8,	#Non_empty_str
	'p'	=> 16,	#sPrintf. {@link http://php.net/sprintf}
	'e'	=> 32,	#Evaluate. Evaluated only ->_name !!!
	'E'	=> 64,	#Evaluate full! Evaluate all as full result.
	'v'	=> 128,	#Value,
	'I'	=> 256,	#Iterate ->_value (or ->_realValue) and each format as ->_format
//	'm'	=> 256,	#Method. Invoke method of ->_value (or ->_realValue)
	);

	private $_format;				#Array of format.
	private $_modStr;				#Modifiers.
	private $_mod;					#Integer of present mods
	private $_modArr = array();		#Array of present mods
	private $_value;				#Value, what processed in this formating.
	private $_realValue;			#If modified (part) in mod_s, mod_a
	private $_realValued = false;	#Flag, to allow pipe through several mods (like as s. a, e)
	private $_name;

	private $_resStr;				#For caching

	/**
	* @method Object(settings) sets() sets() return current settings
	**/

	/**
	* @method Object(HuFormat) cerate() Return new instance of object.
	**/

	/**
	* Constructor
	* {@see ::set}
	*	Be careful - you should explicit provide value like false (invoke as __construct(null, $t = false) for example, because 2d parameter is reference). Otherwise default value null means - using $this as value! 
	* @return
	**/
	public function __construct(array $format = null, &$value = null){
	$this->set($format, $value);
/*
	//Unfortunately a can not Use multiple inheritance. Inherit get_settings is more graceful way.
	runkit_method_copy(__CLASS__, 'sets', 'get_settings', 'sets');
	runkit_method_copy(__CLASS__, 'create', 'Single', 'create');
*/
	}#m __construct

	/**
	* Set main: format and value.
	* @param	array|string	$format
	* @param	&mixed	$value.	{@see ::setValue()}
	* @return	&$this
	**/
	public function &set($format = null, &$value = null){
	$this->setValue($value);

		if (null !== $format) $this->parseInputArray($format);

	return $this;
	}#m set

	/**
	* Return current value.
	* @return	&mixed
	**/
	public function &getValue(){
		if ($this->_realValued) return $this->_realValue;
		else return $this->_value;
	}#m getValue

	/**
	* Set value
	* @param	&mixed	$value.	Value to format.
	* 	If === null $this->_value =& $this; $this->_realValue =& $this->_value; 	 
	* @return	&$this
	**/
	public function &setValue(&$value){
		if(null === $value){
		$this->_value =& $this;
		}
		else $this->_value = $value;
	$this->_realValued = false;
	$this->_resStr = null;
	return $this;
	}#m setValue

	/**
	* Parse incoming format-array and set appropriate properties.
	* Accepts 3 forms of format (in examples ':::' is {@see ::parseModsName()}):
	*	1. All in one Array: [0] - Mods:::Name; [1],[2],[3]...[n] - data.
	*	array(
	*		'sn:::bold_text',	//Mods:::Name
	*		'<b>', '</b>', 'default text'	//Data
	*	)
	*
	*	2. Associative array (hash): Key - Mods:::Name, value - Array of [0] - Mods, [1], [2]...[n] - data.
	*	array(
	*		//Name
	*		'bold_text'	=> array(	//Mods empty, Name
	*			'<b>', '</b>', 'default text'	//Data
	*		)
	*	)
	*
	*	3. Just simply string like 'text to add'. Leaved as is.
	*
	* @param	array|string $format to parse
	* @return	&$this
	**/
	public function &parseInputArray($format){
	$this->_mod = 0;
	$this->_modStr = $this->_name = $this->_resStr = $this->_realValue = null;
	$this->_modArr = array();
	$this->_realValued = false;

		if (is_array($format)){
			if (is_array($format[key($format)])){#<2>
			$this->parseModsName(key($format));
			$this->_format = $format[key($format)];
			}
			else{#<1>
			$this->parseModsName(array_shift($format));
			$this->_format = $format;	#Tail
			}
		}
		else{#<3>
		$this->_name = $this->_realValue = $format;
		$this->_realValued = true;
		}

	return $this;
	}#m parseInputArray

	/**
	* Parses and set from given str. As separator used {@see self::mods_separator}.
	* F.e.: 'AI:::line'. If separator not present - whole string in NAME!
	* @param	string $str
	* @return	&$this
	**/
	protected function &parseModsName($str){
		if (!strstr($str, self::mods_separator)){//Whole name
		$this->_name = $str;
		$this->_modStr = '';
		}
		else{//Separator present
		list ($this->_modStr, $this->_name) = explode(self::mods_separator, $str);
		}
	return $this->parseMods(true);
	}#m parseModsName
	

	/**
	* Construct and return string to represent provided value according given format.
	* @return string
	**/
	public function getString(){
		if (!$this->_resStr){
		$this->_resStr = '';

			foreach ($this->_modArr as $mod){
				if (ctype_upper($mod)){
				$this->_resStr .= call_user_func(array($this, 'mod_'.$mod.$mod));
				}
				else $this->_resStr .= call_user_func(array($this, 'mod_'.$mod));
			}

			//If all mod_* are only evalute value and not produce out.
			if (!$this->_resStr) return $this->getValue();
		}

	return $this->_resStr;
	}#m getString

	/**
	* Set or not?
	* @param	integer $mod.
	* @return boolean
	**/
	public function isMod($mod){
		if (!$this->_mod and $this->_modstr) $this->parseMods();
	return ($this->_mod & $mod);
	}#m isMod

	/**
	* Set, or unset mods.
	* @param	string	$mods. String to set o unset Mods like: '-I+s+n'.
	*	If '-' - unset.
	*	If '+' - set.
	*	If '*' - invert.
	*	If absent - equal to '+'
	* @return	&$this
	* @Throw(VariableRangeException)
	**/
	public function &changeModsStr($mods){
		for($i=0; $i < strlen($mods); $i++){
			if (in_array($mods{$i}, array('+', '-', '*'))){
			$op = $mods{$i};
			$mod = $mods{++$i};
			}
			else{
			$mod = $mods{$i};
			$op = '+';	//Default
			}

			switch ($op){
			case '+':
			$this->_mod |= self::$MODS[$mod];
			break;

			case '-':
			$this->_mod ^= self::$MODS[$mod];
			break;

			case '*':
			$this->_mod &= ~self::$MODS[$mod];
			break;

			default:
			throw new VariableRangeException('Unknown operator - "'.$op.'"');
			}
		}

	$this->parseMods(false);
	return $this;
	}#m changeModsStr

	/**
	* Set Modifiers from string.
	* @param	string $modstr	String of modifiers.
	* @return &$this
	* @Throw(VariableRequiredException)
	**/
	protected function &setModsStr($modstr){
	$this->_modStr = REQUIRED_VAR($modstr);
	$this->parseMods();
	return $this;
	}#m setModsStr

	/**
	* Get string of Modifiers.
	* @return string
	**/
	public function &getModsStr(){
	return implode('', $this->_modArr);
	}#m getModsStr

	/**
	* Get Modifiers.
	* @return integer
	**/
	public function &getMods(){
	return $this->_mod;
	}#m setMods

	/**
	* Set Modifiers.
	* @param	integer	$mods. Modifiers to set. 
	* @return	&$this
	**/
	public function &setMods($mods){
	$this->_mod &= $mods;
	$this->parseMods(false);
	return $this;
	}#m setMods

	/**##########################################################
	* Private and Protected methods							 	*
	##########################################################**/

	/**
	* Parse modifiers from string. 1 char on mod.
	* @param	bolean(true)
	*	True	- from string $this->_modStr
	*	False	- from integer $this-_mod
	* @return	&this
	* @Throw(VariableRangeException)
	**/
	protected function &parseMods($direction = true){
		if ($direction){
		$this->_mod = 0;
			for($i=0; $i < strlen($this->_modStr); $i++){
				if (in_array($this->_modStr{$i}, array_keys(self::$MODS))){
				$this->_mod |= self::$MODS[$this->_modStr{$i}];
				array_push($this->_modArr, $this->_modStr{$i});
				}
				else throw new VariableRangeException('Unknown modifier - '.$this->_modStr{$i});
			}
		}
		else{//Now correct array-values
			foreach (self::$MODS as $key => $M){
				if ($this->isMod($M) and !in_array($key, $this->_modArr)){
				array_push($this->_modArr, $M);
				$this->_modStr .= $M;
				}
				elseif (!$this->isMod($M) and in_array($key, $this->_modArr)){
				$k = array_keys($this->_modArr, $key);
				unset($this->_modArr[$k[0]]);
				$this->_modStr = str_replace($key, '', $this->_modStr);
				}
			}
		}

	//In modifyed mods - must recalculate values
	$this->_realValued = false;
	$this->_resStr = null;

	return $this;
	}#m parseMods

	/**
	* Treat ->_name as property-name
	* @return void
	**/
	protected function mod_s(){
		if (!$this->_realValued){
		$this->_realValue = @$this->_value->{$this->_name};
		$this->_realValued = true;
		}
		else $this->_realValue = $this->_value->{$this->_realValue};
	}#m mod_s

	/**
	* Tread ->_name as index in ->_value
	* @return void
	**/
	protected function mod_a(){
		if (!$this->_realValued){
		$this->_realValue = $this->_value[$this->_name];
		$this->_realValued = true;
		}
		else $this->_realValue = $this->_value[$this->_realValue];
	}#m mod_s

	/**
	* Process ->_value through NON_EMPTY_STR. ->_format must have appropriate values.
	* @return string
	**/
	protected function mod_n(){
	return NON_EMPTY_STR($this->getValue(), @$this->_format[0], @$this->_format[1], @$this->_format[2]);
	}#m mod_n

	/**
	* Procces ->_value through standard sprintf function. All elements self::sprintf_var (def: __vAr__) in ->_format replaced by its
	* real value, and this array go in sprintf
	* @return string
	**/
	protected function mod_p(){
		#Replace by real value.
		foreach (array_keys($this->_format, self::sprintf_var) as $key){
		$this->_format[$key] = $this->_realValue;
		}
	return call_user_func_array('sprintf', $this->_format);
	}#m mod_p

	/**
	* Evalute. Evaluted only ->_value
	* @return	void
	**/
	protected function mod_e(){
		if (!$this->_realValued){
		eval('$this->_realValue = '.$this->_name.';');
		$this->_realValued = true;
		}
		else eval('$this->_realValue = '.$this->_realValue.';');
	}#m mod_e

	/**
	* Evaluete full! Evaluete all as full result.
	* @return string
	**/
	protected function mod_EE(){
	${self::evalute_var} = $this->getValue();
	eval('$ret = '.$this->_format[0].';');
	return $ret;
	}#m mod_E

	/**
	* Value instead name
	* @return	void
	**/
	protected function mod_v(){
		if (!$this->_realValued){
		$this->_realValue = $this->_value;
		$this->_realValued = true;
		}
		else{
		throw new HuFormatException('Got conflicted format modifiers!');
		}
	}#m mod_e

	/**
	* ALL. Recursive parse format
	* @return	string
	**/
	protected function mod_AA(){
	$hf = new self(null, $this->_value);
	$ret = '';
		foreach ($this->_format as $f){
		$hf->parseInputArray($f);
		$ret .= $hf->getString();
		}
	return $ret;
	}#m mod_AA

	/**
	* Iterate by ->_value or ->_realValue.
	* @return	string
	**/
	protected function mod_II(){
	$hf = new self($this->_format, $t = false);
	$ret = '';

		foreach ($this->getValue() as $v){
		$hf->setValue($v);
		$ret .= $hf->getString();
		}
	return $ret;
	}#m mod_II
};#c HuFormat
?><?
/**
* ClassExceptions
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
*
* @changelog
* 2008-05-31 5:26 v 1.0 to 1.1
*	- Add ClassUnknownException
**/



class ClassException extends BaseException{}

class ClassUnknownException extends ClassException{}
class ClassNotExistsException extends ClassException{}
class ClassMethodExistsException extends ClassException{}
class ClassPropertyNotExistsException extends ClassException{}
?><?
/**
* BaseException
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

class BaseException extends Exception{
	#$pos = false - at end, else - in begining
	public function ADDMessage($addmess, $pos = false){
		if (!$pos) $this->message .= $addmess;
		else $this->message = $addmess.$this->message;
	}
}
?><?
/**
* VariablesExceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.1
*
* @changelog
* 2008-05-29 17:51 v 2.0b to 2.1
*	- Fully rewritten and now contructor of VariableRequiredException takes 1st argument backtrace nor Tokenizer!
*	- Added methods VariableRequiredException: ::varName and ::getTokenizer
* 2008-05-30 23:19
*	- Move include of Debug/backtrace.php after declaration class VariableRequiredException to
*	 break cycle of includes
**/


class VariableException extends BaseException{};

class VariableRequiredException extends VariableException{
public $bt = null;
private $var = null;

private $tok_ = null;

	public function __construct(backtrace &$bt, $varname = null, $message = null, $code = 0) {
	$this->bt	= $bt;
	$this->var= $varname;

	// make sure everything is assigned properly
	parent::__construct($message, $code);
	}#c


	/**
	* Return varname
	*
	* @param bool	$noTokenize=false Not try get parameter if it not provided directly.
	* @return string
	**/
	public function varName($noTokenize = false){
		if ($noTokenize){
		return $this->var;
		}

		if ($this->var) return $this->var;
	return $this->getTokenizer()->getArg(0);
	}

	/**
	* Get Tokenizer object, suited to backtrase with instanciated exception.
	* Also create object if it is not exists as yet.
	*
	* @return Object(Tokenizer)
	**/
	public function &getTokenizer(){
		if (!$this->tok_){
			if (!class_exists('Tokenizer')){
				if(@$__CONFIG['debug']['parseCallParam'] or !@NO_DEBUG){
				
				}
			}

			$this->tok_ = Tokenizer::create(
				$this->bt->current()
			)->parseCallArgs();
		}

	return $this->tok_;
	}
};

/**
* It's Before declaration of VariableRequiredException may produce cycle of includes...
**/


class VariableEmptyException		extends VariableRequiredException{}
class VariableIsNullException		extends VariableRequiredException{}

class VariableRangeException		extends VariableException{}
/** Greater than */
class VariableRangeGTException	extends VariableRangeException{}
/** Less than */
class VariableRangeLTException	extends VariableRangeException{}

class VariableArrayInconsistentException extends VariableException{}
?><?
/**
* Toolkit of small functions as "macroses".
* DEBUG version
* @package Macroses
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	2008-05-29 19:55 version 1.2 from 1.0
*	- Rewritten with VariableRequiredException. Now provide only backtrace, not Tokenizer! It takes
*	less overhead and debag/nodebug handled in one place (in exception class)
**/



/**
* Thows {@see VariableRequiredException) if !$var ({@link http://ru2.php.net/manual/ru/types.comparisons.php}).
* In constructor of VariableRequiredException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is usefull in direct operations like assigment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialise them arg of Tokenizer, else real parse.
* @return &mixed
* @Throw(VariableRequiredException)
**/
function &REQUIRED_VAR(&$var, $varname = null){
	if (!$var){
		throw new VariableRequiredException(
			new backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
?><?
/**
* Toolkit of small functions as "macroses".
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Return first NON-empty var if present. Nothing return otherwise (yes, warning will).
* @params	variable amount of arguments.
* @return mixed
**/
function EMPTY_VAR(){
$numargs = func_num_args();
$i=0;
	while (
		$i < $numargs
		 and
		!($res = func_get_arg($i++))
	){/*Nothing do, just skip it */}

	if ($res) return $res;
}
?><?
/**
* Toolkit of small functions as "macroses".
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Assign value of variable if value not (bool)false.
* @param	&mixed	$var
* @param	&mixed	$value
* @return void
**/
function ASSIGN_IF(&$var, &$value){
	if ($value) $var = $value;
}#f ASSIGN_IF
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage Bactrace
* @version 2.1.5.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-30 01:20 v 2.1b to 2.1.1
*	- Move Include debug.php to method ::dump, where only it may be used.
*
* 2008-05-30 14:19  v 2.1.1 to 2.1.2
*	- Add capability to PHP < 5.3.0-dev:
*		* Replace construction ($var ?: "text") to ($var ? '' : "text")
*		* Around "new static" (which is more "correct") in eval. Oterwise php scream what it is not known "static" and get parse error!
*		 return eval('return new static($arr, $N);');
*
* 2008-08-27 20:07 v 2.1.2 to 2.1.3
*	- Modify include and check conditions in formatArgs() and printout() methods
*
* 2008-09-07 22:02 v 2.1.3 to 2.1.4
*	- In methods printout() and formatArgs() add cache of $OutType. Fix errors in them with inclusion (see comment below.).
*
* 2008-09-14 21:48 v 2.1.4 to 2.1.5
*	- Add class-exception BacktraceEmpty
*	- Add check to non-empty backtrace before formatting it in printout method. Now it may throw BacktraceEmptyException  
*
* 2008-09-15 17:34 v 2.1.5 to 2.1.5.1
*	- Delete some excessive debug comments.
**/








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
		/**
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
	* Return property, if it exists, Throw ClassPropertyNotExistsException
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
	* @param	string	$name	Name of required property
	* @return boolean
	**/
	public function __isset($name) {
		if (!in_array($name, backtraceNode::$properties)) throw new ClassPropertyNotExistsException('Property <'.$name.'> does NOT exist!');

	return isset($this->_btn[$name]);
	}#m __isset

	/**
	* Dump in appropriate(auto) form bactraceNode.
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
	* @param Object(backtraceNode)	$toCmp Node compare to
	* @return integer. 0 if equals. Other otherwise (> or < not defined, but *may be* done later).
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
	* @return
	**/
	public function setArgsFormat($format){
	$this->_format = REQUIRED_VAR($format);
	}#m setArgsFormat

	/**
	* Return string of formated args
	* @param array=null		$format
	*	If null, trying from ->_format set in {@see ::setSrgsFormat()}, and finaly
	*		get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param integer		$OutType	If present - determine type of format from $format (passed or default). Must be index in $format.
	* @return string
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
				( /*-One- (include_once('Debug/_HuFormat.defaults/backtrace::printout.php')) ++>true*/ (false) || true )
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
	* @param	array	$bt	Array as result debug_backtrace() or it part. If null filled by
	*	direct debug_backtrace() call.
	* @param	int=1	$removeSelf	If filled automaticaly, containts also this call
	*	(or call ::create() if appropriate). This will remove it. Number is amount of arrays
	*	remove from stack.
	* @return Object(backtrace)
	**/
	public function __construct(array $bt = null, $removeSelf = 1){
		if ($bt) $this->_bt = $bt;
		else $this->_bt = debug_backtrace();

		while ($removeSelf--) array_shift($this->_bt);
	}#m __construct

	/**
	* To allow constructions like: backtrace::create()->methodName()
	* @param	array	$bt	{@link ::__construct}
	* @param	int=2	$removeSelf	{@link ::__construct}
	* @return	backtrace
	**/
	static public function create(array $bt = null, $removeSelf = 2){
	return new self($bt, $removeSelf);
	}#m create

	/**
	* Dump in appropriate(auto) form bactrace.
	*	Fast dump of current backtrase may be invoked as backtrace::create()->dump();
	* @param	boolean	$return
	* @param	string	$header('_debug_bactrace()')
	* @return	mixed	return auto::a(...)
	**/
	public function dump($return = false, $header = '_debug_bactrace()'){
	
	return dump::a($this->_bt, $header, $return);
	}#m dump

	/**
	* Get BackTraceNode
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
	* @param	integer	$N	Place to node. If not exists - silently create.
	*	{@see ::getNumberOfNode() fo more description}
	* @return void
	**/
	public function setNode($N = null, backtraceNode $node){
	$this->_bt[ $this->getNumberOfNode($N) ] = $node;	
	}#m setNode

	/**
	* Return real number of requested Node in _bt array, implements next logic:
 	*	If $N === null set on current node ({@see ::current()}).
	*	If $N < 0	Negative values to to refer in backward: -2 mean: sizeof(debug_backtrace() - 2)!
	*		Be carefull value -1 meaning LAST element, not second from end!
	* 
	* @param	integer	$N	
	* @return integer	Number of requested node.
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
	*	{@see ::getNumberOfNode() fo more description}
	* @return void
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
	* @return	integer
	**/
	public function length(){
	return sizeof($this->_bt);
	}#m length

	/**
	* Find node of bactrace. To match each possible used fnmatch (http://php.net/fnmatch), 
	* so all it patterns and syntrax allowed.
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
	* @param boolean=false	$return	Return or print directly.
	* @param array=null		$format
	*	If null, trying from format set in {@see ::setPrintoutFormat()}, and finaly
	*		get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param integer=null	$OutType	If present - determine type of format from $format (passed or default). Must be index in $format.
	* @Throw(VariableRequiredException, BacktraceEmptyException)
	**/
	//Was: public static function dump::backtrace($part = null, $return = false){
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
				( /*-One- (include_once('Debug/_HuFormat.defaults/backtrace::printout.php')) ++>true*/ (false) || true )
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
	* @return
	**/
	public function setPrintoutFormat($format){
	$this->_format = REQUIRED_VAR($format);
	}#m setPrintoutFormat

	/**
	* @ToDo Implement __toString method
	* DescrHere
	* @param
	* @return
	**/
/*
	public function __toString(){
//	return 'Object(BackTrace)';
	return $this->printout(null, true);
	}#m __toString
*/

/**#########################################################
* From interface Iterator
* Use self indexing to allow delete nodes and continue loop foreach.
##########################################################*/
	/**
	* Rewind internal pointer to begin
	* @return void
	**/
	public function rewind(){
	$this->_curNode = 0;
	}#m rewind

	/**
	* Return current backtraceNode
	* @return Object(backtraceNode)|null
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
	* @return integer
	**/
	public function key(){
	return $this->_curNode;
	}#m key

	/**
	* Return next backtraceNode
	* @return Object(backtraceNode)|null
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
	* @return boolean
	**/
	public function valid(){
	return ($this->current() !== null);
	}#m valid

	/**
	* Return end backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	* @return Object(backtraceNode)
	**/
	public function end(){
	return $this->getNode( ($this->_curNode = $this->length() - 1) );
	}#m end

	/**
	* Return prev backtraceNode and move internal pointer to it. It is NOT part Iterator interface
	*	and added to more flexibility.
	* @return Object(backtraceNode)|null
	**/
	public function prev(){
		if ($this->_curNode < 1) return null;

	return $this->getNode( --$this->_curNode );
	}#m prev
}#c backtrace
?>
<?
/**
* Debug and backtrace toolkit.
* @package Debug
* @version 2.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* In call function funcName($currentValue); in any place, in function by other methods available only
* value of variable $currentValue but name call-time (in this example '$currentValue') - NOT.
*
* This return array of names CALL parameters!
* Implementation is UGLY - view in source PHP files and parse it, but I NOT known other way!!!
*
* In number of array in debug_backtrace().
*
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
**/

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
	* @param array|Object(backtraceNode) $db	Array, one of is subarrays from return result by debug_backtrace();
	* @Throws(VariableRequiredException)
	* @return $this
	*/
	public function __construct(/* array | backtraceNode */ $db = array()){
		if (is_array($db)) $this->setFromBTN(new backtraceNode($db));
		$this->setFromBTN($db);
	}#m __construct

	/**
	* Set from Object(backtraceNode).
	* {@inheritdoc ::__construct()}
	* @return &$this
	*/
	public function &setFromBTN(backtraceNode $db){
	$this->clear();
	$this->_debugBacktrace = $db;
	return $this;
	}#m setFromBTN

	/**
	* To allow constructions like: Tokenizer::create()->methodName()
	* {@inheritdoc ::__construct()}
	*/
	static public function create(/* array | backtraceNode */ $db){
	return new self($db);
	}#m create

	public function clear(){
	#Fill all to defaults
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
	* @description Return string of parsed argument by it number (index from 0). Bounds not checked!
	* @param integer $n - Number of interesting argument.
	* @return string
	**/
	public function getArg($n, $trim = true){
		if ($trim) return trim($this->_args[$n]);
		else return $this->_args[$n];
	}#m getArg

	/**
	* Set to arg new value.
	* @param	integer	$n - Number of interesting argument. Bounds not checked!
	* @param	mixed	$value Value to set.
	* @return	&$this
	**/
	public function &setArg($n, $value){
	$this->_args[$n] = $value;
	return $this;
	}#m setArg

	/**
	* @description Return array of all parsed arguments.
	* @return array
	*/
	public function getArgs(){
	return $this->_args;
	}#m getArgs

	/**
	* @description Return count of parsed arguments.
	* @return integer
	*/
	public function countArgs(){
	return sizeof($this->_args);
	}#m countArgs

	/**
	* @description
	* Search full text of call in src php-file
	* @return $this
	*/
	protected function findTextCall(){
	$this->_filePhpSrc = new file_base(REQUIRED_VAR($this->_debugBacktrace->file));
	$this->_filePhpSrc->loadContent();

	$rega = '/'
		.RegExp_pcre::quote(@$this->_debugBacktrace->type)	#For classes '->' or '::'. For regular functions not exist.
		.'\b'.$this->_debugBacktrace->function	#In case of method and regular function same name present.
		.'\s*\((.*?)\s*\)'	#call
		.'/xms';
	//c_dump($rega, '$rega');

	$this->_regexp = new RegExp_pcre($rega, $this->_filePhpSrc->getBLOB());
	$this->_regexp->doMatchAll(PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
	//c_dump($regexp->getMatches(), 'ALL_matches');
	return $this;
	}#m findTextCall

	/**
	* See description on begin of file ->_debugBacktrace->line not correct start call-line if call
	* continued on more then one string!
	* Seek closest-back line from found matches. In other words, search start of call.
	* So, in any case, I do not have chance separate calls :( , if it presents more then one in string!
	* Found and peek first call in string, other not handled on this moment.
	*
	* @return $this;
	*/
	protected function findCallStrings(){
		if (!$this->_regexp) $this->findTextCall();
	$delta = PHP_INT_MAX;
	$this->_callStartLine = 0;

		//Search closest line
		foreach ($this->_regexp->getMatches() as $k => $match){
		$lineN = $this->_filePhpSrc->getLineByOffset($match[0][1]) + 1;	#Indexing from 0
			if ( ($d = $this->_debugBacktrace->line - $lineN) >= 0 and $d < $delta){
			$delta = $d;
			$this->_callStartLine = $lineN;
			}
			else break;#Not needed more
		}
	//c_dump($delta, '$delta');
	//c_dump($realStartLine, '$realStartLine');

	/*
	$linesOfCall = $this->_filePhpSrc->getLines(array($realStartLine - 1, $delta + 1));
	c_dump($linesOfCall, 'Lines of Call');
	$linesOfCall = implode($this->_filePhpSrc->getLineSep(), $linesOfCall);
	c_dump($linesOfCall, 'Lines of Call');
	$tokens = token_get_all('<?' . $linesOfCall . '?>');
	*/
	$this->_callText = implode(
		$this->_filePhpSrc->getLineSep(),
		$this->_filePhpSrc->getLines(
			array(
			$this->_callStartLine - 1,
			$delta + 1
			)
		)
	);
	}#m findCallStrings

	public function parseTokens(){
		if (!$this->_callText) $this->findCallStrings();
	//c_dump($this->_callText, '$this->_callText');
	#Without start and end tags not parsed properly.
	$this->_tokens = token_get_all('<?' . $this->_callText . '?>');
	return $this;
	}#m parseTokens


	/**
	* Working horse!
	* Base idea from: http://ru2.php.net/manual/ru/ref.tokenizer.php
	* @param boolean(true) $stripWhitespace = False! Because stripped any space, not only on
	*	start and end of arg! This is may be not wanted behavior on constructions like:
	*	$a instance of A. Instead see option $trim in {@link ::getArg()) method.
	* @param boolean(false) $stripComments = false
	* @return $this
	*/
	public function &parseCallArgs($stripWhitespace = false, $stripComments = false){
		if ($this->_tokens === null) $this->parseTokens();
	//c_dump($this->_tokens, '$this->_tokens');

	$this->skipToStartCallArguments();
	$this->addArg();
	$sParenthesis = 0;	#stack
	$sz = sizeof($this->_tokens);	#Speed Up
		while ($this->_curTokPos < $sz){
		$token =& $this->_tokens[$this->_curTokPos++];
		//c_dump($token, '$token');

			if (is_string($token)){
				switch($token){
				case '(':
				++$sParenthesis;
					#Self ( - do not want
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
			//c_dump(token_name($token[0]));
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
	* @return $this
	*/
	private function skipToStartCallArguments(){
	$sz = sizeof($this->_tokens);	#Speed Up
		while ($this->_curTokPos < $sz){
		$token =& $this->_tokens[$this->_curTokPos++];
			if (is_array($token) and T_STRING == $token[0] and $token[1] == $this->_debugBacktrace->function){
			return;
			}
		}
	return $this;
	}#m skipToStartCallArguments

	/**
	* @description. Add text to CURRENT arg.
	* @return noting
	*/
	private function addToArg($str){
	$this->_args[$this->countArgs() - 1] .= $str;
	}#m addToArg

	/**
	* @description. Add next arg to array
	* @return nothing
	*/
	private function addArg(){
	$this->_args[$this->countArgs()] = '';
	}#m addArg

	/**
	* Strip quotes on start and end of argument.
	* Paired
	* @param	string	$arg	Argument to process.
	* @param	boolean	$all If true - all trim, else (by default) - only paired (if only ended with quote, or only started - leaf it as is).
	* @return	string
	*/
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
?><?
/**
* Debug and backtrace toolkit.
* @package Debug
* @subpackage Dump-utils
* @version 2.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-06-26 03:58
*	- Add in transformCorrect_print_r transformation empty array into 1 string ( /Array(0){\s+}/ -> Array(0){} )
**/

class dump_utils{
	/**
	* Transform string, which is text-representation of requested var into more well formated form.
	* print_r variant
	* @param string $dump String returned by print_r
	* @return string. Transformed, well-formated.
	**/
	static public function transformCorrect_print_r($dump){
	return
		trim(
			preg_replace(
				array(
					'/Array\n\s*\(/',
					'/Object\n\s*\(/',
					'/\["(.+)"\]=>\n /',
					'/Array(0){\s+}/',
				),
				array(
					'Array(',
					'Object(',
					'[\1]=>',
					'Array(0){}',
				),
				$dump
			)
		);
	}#m transformCorrect_print_r

	/**
	* Transform string, which is text-representation of requested var into more well formated form.
	* var_dump variant
	* @param string $dump String returned by var_dump
	* @return string. Transformed, well-formated.
	**/
	static public function transformCorrect_var_dump($dump){
	return
		trim(/* For var_dump variant */
			preg_replace(
				array(
					'/array(\(\d+\))\s+({)/i',
					'/Object\n\s*\(/',
					'/\["?(.+?)"?\]=>\n\s*/',
				),
				array(
					'Array\1\2',
					'Object(',
					'[\1] => ',
				),
				$dump
			)
		);
	}#m transformCorrect_var_dump
}; #c dump_utils
?><?
/**
* Debug and backtrace toolkit.
* @package Debug
* @subpackage Debug
* @version 2.3.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-29 15:58 Version 2.3 from 2.2.b
*	- Add config-parameter "display_errors", default true.
*	- Move methods transformCorrect_print_r and transformCorrect_var_dump to separate class dump_utils (dump_utils.php)
*	- Move dump::log into log_dump.php in separate function log_dump.
*		dump::log ReRealise with it.
* 	It is isfull fo not only debug purpose, and very bad what it depends from much debug-tools (classes, functions, files)
* 2008-06-06 16:40 Ver 2.3 to 2.3.1
*	- Include Debug/log_dump.php (in dump::log) and realize dump::log through log_dump free function.
*	- Delete all deprecated free functions!
*
* 2008-08-27 19:15 Ver 2.3.1 to 2.3.2
*	- Handle xdebug.overload_var_dump option in dump::w
*
* 2008-09-15 22:15 Ver 2.3.2 to 2.3.3
*	- Prevent html-output in dump::c evenif if html_errors=On  
**/

define ('DUMP_DO_NOT_DEFINE_STUMP_DUMP', true);



define ('NO_DEBUG', false);

	#Even here, used directly $GLOBALS, because it may be included in other scope (e.g. from function)
	if (!isset($GLOBALS['__CONFIG']['debug'])){
	$GLOBALS['__CONFIG']['debug'] = array(
		/**
		* Parsing what parameters present at call time.
		* For example:
		* dump::c($ttt)
		* is equivalent to
		* dump::c($ttt, '$ttt')
		* This future is very usefull, but require Tokenizer class and got time overhead.
		**/
		'parseCallParam'	=> true,
		/**
		* Set error_reporting to this value.
		* Null has special means - no change!
		**/
		'errorReporting'	=> E_ALL,

		/**
		* Enable or disable global errors reporting.
		**/
		'display_errors'	=> 1,

		/**
		* Provide capability to disable Tokenizer
		* Warning: parseCallParam=true also disable Tokenizer and backtrace
		**/
		'whithout_Tokenizer'=> false
	);
	}

	if (null !== $GLOBALS['__CONFIG']['debug']['errorReporting']){
	error_reporting($GLOBALS['__CONFIG']['debug']['errorReporting']);
	}

	if (null !== $GLOBALS['__CONFIG']['debug']['display_errors']){
	ini_set('display_errors', $GLOBALS['__CONFIG']['debug']['display_errors']);
	}

	if (@$GLOBALS['__CONFIG']['debug']['parseCallParam']){
	
	
	}

/**
* @package Debug
* Mainly for emulate namespace
* Most (all?) methods are static
**/
class dump extends dump_utils{
	/**
	* Return $header. If in $header present - return as is, else make guess as real be invoked.
	* @param &mixed $header. Be careful! By default, in parent methods like dump::*() $heade=false!
	*	If passed $header === null it allows distinguish what it is not passed by default or
	*	it is not needed!!
	* @return &mixed $var
	**/
	static public function getHeader(&$header, &$var){
		if ($header) return $header;
		elseif(
			//Be careful! Null, NOT false by default in dump::*()! It allows distinguish what it is
			//not passed by default or it is not needed!!
			$header !== null
			and
			@$GLOBALS['__CONFIG']['debug']['parseCallParam']
			and
			(
				$cp = Tokenizer::trimQuotes(
					Tokenizer::create(
						backtrace::create()->find(
							backtraceNode::create(
								array(
									'class'	=> 'dump',
									'function'=> '[awc]',
									'type'	=> '::'
								)
							)
						)->end()
					)->parseCallArgs()->getArg(0)
				)
			)
			!= ( is_object($var) ? spl_object_hash($var) : (string)$var ) /* PHP Catchable fatal error NOT handled traditionaly
			with try-catch block!
			See http://ru2.php.net/manual/en/migration52.error-messages.php
			and http://www.zend.com/forums/index.php?t=rview&th=2607&goto=6920
			*/
			) return $cp;
//		else return 'Unknown';
	}#m getHeader

	/**
	* Console dump. Useful in cli-php. See also {@link ::a()} and {@link ::auto()}
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function c($var, $header = false, $return = false){
	$ret = '';

		if ($header = self::getHeader($header, $var)) $ret .= "\033[1m".$header."\033[0m: ";

	ob_start();
		//This may happens. F.e. it presentin int template class
		if ($return_html_errors = ini_get('html_errors')){
		ini_set('html_errors', false);
		}
	var_dump($var);//This isn't possible return string in other way, such as it possible in print_r(, true)
	$dStr = ob_get_clean();
	$ret .= self::transformCorrect_var_dump($dStr)."\n";

		if ($return_html_errors) //Revertb back
		ini_set('html_errors', true);

		if ($return) return $ret;
		else echo $ret;
	}#m c

	/**
	* Log dump. Useful to return string for file-write. See also {@link ::a()} and {@link ::auto()}
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function log($var, $header = false, $return = true){
	
	return log_dump($var, $header, $return);
	}#m log

	/**
	* Buffered dump. Useful to return string for file-write. See also {@link ::a()} and {@link ::auto()}
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param string|array	Callback-function or array(object, 'method')
	* @return string|void	Depend of parameter $return
	**/
	static public function buff($var, $header = false, $debug_func = 'print_r'){
	/**
	* For use with family ob_*!
	* In this case do not restricted use standart print_r, var_dump and var_export
	*
	* Out to stderr, instead of stdout
	* This is "no good" method, but it is worked for me.
	* $extra may contain only SHORT aliases!
	*/
	$header = self::getHeader($header, $var);

	$print_func = ' '.$debug_func;
	$cmd = 'echo "<? '.$print_func.'(unserialize('.addcslashes(escapeshellarg(serialize($var)),'"').')'.($extra ? ",'".addcslashes($header, '$')."'" : '').');?>" | php';
	file_put_contents('php://stderr', shell_exec($cmd));
	}#m buff

	/**
	* Short alias to Buffered Console Dump. Parameters are same. See appropriate methods
	**/
	static public function b_c($var, $header = false){
	$header = self::getHeader($header, $var);

		return dump::buff($var, $header, 'dump::c');
	}#m b_c

	/**
	* WEB dump. Useful to dump in Web-browser. See also {@link ::a()} and {@link ::auto()}
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function w($var, $header = false, $return = false){
	$ret = '';
		if ($header = self::getHeader($header, $var)) $ret .= '<h4 style="color:green">'.$header.":</h4>\n";

	ob_start();
	var_dump($var);//This isn't possible return string in other way, such as it possible in print_r(, true)
	$dStr = ob_get_clean();

		#if (ini_get('xdebug.overload_var_dump')){
		# Config-directives not always is set...
		if ('<pre' == substr($dStr, 0, 4)){
		$ret .= $dStr;
		}
		else{//By hand
		$ret .= '<pre><xmp>';
		// $ret .= self::transformCorrect_print_r(print_r($var, true))."\n";
		$ret .= self::transformCorrect_var_dump($dStr)."\n";
		$ret .= '</xmp></pre>';
		}

		if ($return) return $ret;
		else echo $ret;
	}#m w

	/**
	* WAP dump. Useful to dump in WAP-browser (XML).
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function wap($var, $header = false, $return = false){
	$ret = '';
		if ($header) $ret .= '<h4>'.$header."</h4>\n";	#Only explicitly given
	$ret .= nl2br(print_r($var, true)).'<br />';
		if ($return) return $ret;
		else echo $ret;
	}#m wap

	/**
	* Make guess how invoked from cli or from WEB-server (any other) and turn next to c_dump or w_dump respectively.
	* @return mixed	::c or ::w invoke whith same parameters.
	**/
	static public function auto($var, $header = false, $return = false){
		/**
		* May use php_sapi_name() or (in notice of this) constant PHP_SAPI. Use second.
		*/
		if (PHP_SAPI == 'cli') return self::c($var, $header, $return);
		else return self::w($var, $header, $return);
	}#m auto

	/**
	* Only short alias for {@link ::auto()}, nothing more!
	* @return mixed	::c() or ::w() invoke whith same parameters.
	**/
	static public function a($var, $header = false, $return = false){
	return self::auto($var, $header, $return);
	}#m a

	/**
	* One name to invoke dependently by out type.
	* @return mixed One of result call: ::c, ::a, ::log, ::wap.
	* @Throw(VariableRangeException)
	**/
	public static function byOutType($type, $var, $header = false, $return = false){
	
		switch ($type){
		case OS::OUT_TYPE_BROWSER:
		return self::w($var, $header, $return);
		break;

		case OS::OUT_TYPE_CONSOLE:
		return self::c($var, $header, $return);
		break;

		case OS::OUT_TYPE_FILE:
		return self::log($var, $header, $return);
		break;

		case OS::OUT_TYPE_WAP:
		return self::wap($var, $header, $return);
		break;

		#Addition
		case OS::OUT_TYPE_PRINT:
		return self::a($var, $header, $return);
		break;

		default:
		
		throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m byOutType
}#c debug
?>