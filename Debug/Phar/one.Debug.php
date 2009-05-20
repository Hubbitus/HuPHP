<?
/** This is automaticaly generated file. Please, do not edit it! **/
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @version 2.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-12-06 17:20 ver 2.0 to 2.1
*	Correct argtype Array and String for log (FORMAT_FILE) output. Make it same as for console
**/


/**
* Helper to more flexibility show large amount of data (long strings, dump of arrays etc.)
*
* @param	string	$shortVar
* @param	string	$longVar
* @param	string('<textarea')	$innerTagStart
* @param	string('</textarea>')	$innerTagEnd
* @return	string
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
			'string'	=> array('E:::', backtrace__printout_WEB_helper('\\\'\'.htmlspecialchars(substr($var, 0, 32)).((($sl = strlen($var)) < 32) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}', '\'.htmlspecialchars($var).\'')),
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
						array('E:::args', '"\033[33m(\033[0m".$var->formatArgs()."\033[33m)\033[0m"'),
						"\n",

						"\t->\033[31;1mfile: \033[0m",
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
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']	=  $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']	=& $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_WEB']['argtypes'];
#Difference in argTypes
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']['string']
	= $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['string']
	= array('E:::', '\'\\\'\'.htmlspecialchars(substr($var, 0, 28)).((($sl = strlen($var)) < 28) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}\'');
$GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_FILE']['argtypes']['array']
	= $GLOBALS['__CONFIG']['backtrace::printout']['FORMAT_CONSOLE']['argtypes']['array']
	= array('E:::', '\'Array(\'.count($var).\')\'');

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
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0b
*
* @changelog
*	* 2008-08-27 ver 1.0 to 1.1
*	- Added methods: clearPendingWrite(), __destructor(), appendString()
*
*	* 2009-01-25 00:00 ver 1.1 to 1.2
*	- Modify setPath() to set full path into ->filename. ->rawFilename filled also.
*	- Add method: rawPath().
*	- Add  (for the OS::isPathAbsolute)
*
*	* 2009-02-26 15:59 ver 1.2 to 1.2.1
*	- Add in setPath initial initialization of $this->filename in any case! In case
*		if path is relative it will expanded. If not - old
*		behaviour it is not initialised!
*
*	* 2009-03-23 16:44 ver 1.2.1 to 1.2.2
*	- Method ::loadContent() changed to @return	&$this;. Full PhpDoc writed.
*
*	* 2009-03-25 10:50 ver 1.2.2 to 2.0b
*	- Major changes. Make @package Filesystem and split current file_base class to 3:
*		o file_base - base is base. Make it abstract.
*		o file_inmem - based on file() or file_get_contents. Operate by whole contents of file in memmory.
*		o file_read - based on fopen/fread/fwrite. Provide operations of continios read, seek, byt read...
*	- Write PhpDoc'umentation fo all methods for comfortable usage.
*	- Remove deprecated method ::loadByLines(). Last time it was only alias to ::loadContent() - use it instead.
*	- Fix few minor bugs (in ::implodeLines(), ::getBLOB())...
*	- Method ::writeContents() renamed to ::writeContent()
*	- Acces type of method ::checkOpenError() changed from private to protected.
**/






/**
* Base class for most file-related operations.
**/
class file_base{
private $filename = '';
private $rawFilename = '';	#Filename to try open. For error-reports.
private $dir = '';

protected $_writePending = false;

/** Pending content for write **/
protected	$content;

	/**
	* Construct new object with provided (optional) path (URL).
	*
	* @param	string	$filename
	**/
	public function __construct($filename = ''){
		if ($filename) $this->setPath($filename);
	}#__c

	/**
	* Write all pendings write if it wasn't be done manually before. This is to avoid data loss.
	**/
	public function __destruct(){
		if ($this->_writePending) $this->writeContent();
	}#__d

	/**
	* Set new path. For example to writing new file.
	*
	* @param	string	$filename	New filename
	* @return	&$this
	**/
	public function &setPath($filename){
	$this->filename = $this->rawFilename = $filename;
	/**
	* And we MUST set full path in ->filename because after f.e. chdir(...) relative path may change sense.
	* Additionally, in __destruct call to getcwd return '/'!!! {@See http://bugs.php.net/bug.php?id=30210}
	**/
		// We can't direct use $this->filename instead of $realpath because if it ! we not always want null it!
		if (!($realpath = realpath($this->rawFilename))){
			/**
			* Realpath may fail because file not found. But we can't agree with that,
			* because setPath may be invoked to set path for write new (create) file!
			* So, we try manually construct current full path (see abowe why we should do it)
			**/
			if (! OS::isPathAbsolute($this->rawFilename)){
			$this->filename = getcwd() . DIRECTORY_SEPARATOR . $this->rawFilename;
			}
		}
		else $this->filename = $realpath;
	return $this;
	}#m setPath

	/**
	* Return curent path
	*
	* @return	string
	**/
	public function path(){
	return $this->filename;
	}#m path

	/**
	* Return curent RAW (what wich be passed into the {@see setPath()}, without any transformation) path.
	*
	* @return	string
	**/
	public function rawPath(){
	return $this->rawFilename;
	}#m rawPath

	/**
	* Return true if current set path is exists.
	*
	* @return	boolean
	**/
	public function isExists(){
	#Very strange: file_exists('') === true!!!
	return ('' != $this->path() and file_exists($this->path()));
	}#m isExists

	/**
	* Return true, if file on current path is readable.
	*
	* @return	boolean
	**/
	public function isReadable(){
	return is_readable($this->path());
	}#m isReadable

	/**
	* Return directory part of current path (file must not be exist!).
	*
	* @return	string
	**/
	public function getDir(){
	return dirname($this->path());
	}#m getDir

	/**
	* Clear pending writes.
	*
	* @return	&$this
	**/
	public function &clearPendingWrite(){
	$this->_writePending = false;
	return $this;
	}#m clearPendingWrite

	/**
	* Set content for write.
	*
	* @param string	$string. String to set from.
	* @return &$this
	* @Throw(VariableRequiredException)
	**/
	public function &setContentFromString($string){
	$this->content = REQUIRED_VAR($string);
	$this->_writePending = true;
	return $this;
	}#m setContentFromString

	/**
	* Append string to pending write buffer.
	*
	* @param	string	$string. String to append from.
	* @return	&$this
	* @Throw(VariableRequiredException)
	**/
	public function &appendString($string){
	$this->content += REQUIRED_VAR($string);
	$this->_writePending = true;
	return $this;
	}#m appendString

	/**
	* Write whole content to file (filename may be set via ->setPath('NewFileName'))
	*
	* @param	integer	flags See {@link http://php.net/file_put_contents}
	* @param	resource	$resource_context See {@link http://php.net/stream-context-create}
	* @return	integer	Count of written bytes
	**/
	public function writeContent($flags = null, $resource_context = null){
	$this->checkOpenError(
		#$this->rawFilename because may be file generally not exists!
		(bool) ($count = @file_put_contents($this->path(), $this->content, $flags, $resource_context))
	);
	$this->_writePending = false;
	return $count;
	}#m writeContent

	############################################
	#####private functions
	############################################
	protected function checkOpenError($succ){
		if ( ! $succ ){
			if (!$this->isExists()) throw new FileNotExistsException('File not found', $this->path());
			if (!$this->isReadable()) throw new FileNotReadableException('File not readable. Check permissions.', $this->path());
			throw new FileNotReadableException('Unknown error get file.', $this->path());
		}
	}#m checkOpenError
}#c file_base
?><?
/**
* Operations with file in memory.
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0b
*
* @changelog
*	* 2009-03-25 13:51 ver 2.0b
*	- Initial SPLITTED version. See changelog of file_base.php
*	- Fix few minor bugs (in ::implodeLines(), ::getBLOB())...
*	- Change methods ::setLineSep() and ::checkLoad() to return &$this
**/



class file_inmem extends file_base{
private $lineContent = null;

private $_lineSep = "\n";		#Unix by default

private $_linesOffsets = array();	#Cache For ->getLineByOffset and ->getOffsetByLine methods

	/**
	* Load full content of file into memmory.
	*
	* If file very big consider read it for example by lines, if task allow it.
	* @todo Split 2 such approach into child classes
	*
	* @param	boolean	$use_include_path
	* @param	resource	$resource_context
	* @param	integer	$offset
	* @param	integer	$maxlen
	* @return	&$this;
	**/
	public function &loadContent($use_include_path = false, $resource_context = null, $offset = null, $maxlen = null){
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
	return $this;
	}#m loadContent

	/**
	* @inheritdoc
	**/
	public function &setContentFromString($string){
	$this->lineContent = array();
	$this->_linesOffsets = array();
	return parent::setContentFromString($string);
	}#m setContentFromString

	/**
	* Partial write not supported, reset full string to resplit by lines it in future.
	* @inheritdoc
	**/
	public function &appendString($string){
	return $this->setContentFromString($this->content . REQUIRED_VAR($string));
	}#m appendString

	/**
	* @inheritdoc
	*
	* Additional parameters are:
	* @param	string	$implodeWith See descr ->implodeLines()
	* @param	boolean	$updateLineSep See descr ->implodeLines()
	**/
	public function writeContent($flags = null, $resource_context = null, $implodeWith = null, $updateLineSep = true){
	$this->checkOpenError(
		#$this->rawFilename because may be file generally not exists!
		(bool) ($count = @file_put_contents($this->path(), $this->getBLOB($implodeWith, $updateLineSep), $flags, $resource_context))
	);
	$this->_writePending = false;
	return $count;
	}#m writeContent

###########################
### Self introduced methods
###########################
	/**
	* Return array of specified lines or all by default
	*
	* @param	array $lines. If empty array - whole array of lines. Else
	*	Array(int $offset  [, int $length  [, bool $preserve_keys  ]] ). See http://php.net/array_slice
	* @param	boolean(true) $updateLineSep. See explanation in ->explodeLines() method.
	* @return	array Array of lines
	* @Throw(VariableEmptyException)
	**/
	public function getLines(array $lines = array(), $updateLineSep = true){
	$this->checkLoad();
		if (!$this->lineContent) $this->explodeLines($updateLineSep);

		if(!empty($lines)) return call_user_func_array('array_slice', array_merge(array( 0 => $this->lineContent), $lines) );
		else return $this->lineContent;
	}#m getLines

	/**
	* Explode loaded content to lines.
	*
	* @param	boolean $updateLineSep if true - update lineSep by presented in whole content.
	**/
	protected function explodeLines($updateLineSep = true){
	preg_match_all('/(.*?)([\n\r])/', $this->content, $this->lineContent, PREG_PATTERN_ORDER);
		if ($updateLineSep) $this->_lineSep = $this->lineContent[2][0/*Any realy. Assuming all equal.*/];
	$this->lineContent = $this->lineContent[1];
	$this->_linesOffsets = array();
	}#m explodeLines

	/**
	* Implode lineContent to whole contents.
	*
	* @param	string	$implodeWith String implode with. If null, by default - ->_lineSep.
	* @param	boolean	$updateLineSep if true - update lineSep by presented $implodeWith.
	**/
	protected function implodeLines($implodeWith = null, $updateLineSep = true){
		if ($implodeWith and $updateLineSep) $this->setLineSep($implodeWith);
	$this->_linesOffsets = array();
	return ($this->content = implode($implodeWith, $this->lineContent)); //Set or not, implode as requested.
	}#m implodeLines

	/**
	* Return string of content
	*
	* @param	string	$implodeWith See descr ->implodeLines()
	* @param	boolean	$updateLineSep See descr ->implodeLines()
	* @return	string
	**/
	public function getBLOB($implodeWith = null, $updateLineSep = true){
		if (
			! $this->content
			or
			( $implodeWith and $implodeWith != $this->_lineSep)
		)
		$this->implodeLines($implodeWith, $updateLineSep);
	return $this->content;
	}#m getBLOB

	/**
	* Get current used line separator.
	* @return	string
	**/
	public function getLineSep() {
	return $this->_lineSep;
	}#m getLineSep

	/**
	* Set new line separator.
	*
	* It also may be used to convert line separators like:
	* $f = new file_inmem('filename');
	* $f->setLineSep("\r\n")->loadContent()->setLineSep("\n")->writeContent();
	*	or even more easy:
	* $f->setLineSep("\r\n")->loadContent()->->writeContent(nul, null, "\n");
	*
	* @param	string	$newSep
	* @return	&$this
	**/
	public function &setLineSep($newSep) {
	$this->_leneSep = $newSep;
	$this->_linesOffsets = array();
	return $this;
	}#m getLineSep

	/**
	* Return line with requested number.
	*
	* Boundaries NOT checked!
	*
	* @param	int	$line
	* @return	string
	**/
	public function getLineAt($line){
		if (!$this->lineContent) $this->explodeLines($updateLineSep);
	return $this->lineContent[$line];
	}#m getLineAt

	/**
	* Calculate line number by file offset.
	*
	* @param	integer	$offset
	* @return	integer
	* @Throw(VariableRangeException)
	**/
	public function getLineByOffset($offset){
		if (!$this->_linesOffsets) $this->makeCacheLineOffsets();
		if ($offset > $this->_linesOffsets[sizeof($this->_linesOffsets)-1][1])
		throw new VariableRangeException('Overflow! This offset does not exists.');

	#Data ordered - provide binary search as alternative to array_search
	$size = sizeof($this->_linesOffsets) - 1;	#For speed up only
	$left = 0; $right = $size;	#Points of interval
	$found = false;
	$line = ceil($size / 2);
		/*
		* Boundary conditions. Additional check of lowest value is mandatory, if used ceil() (0 is not accessible).
		* Additional check of highest value addad only to efficient
		* adjusting, because on it point the maximum time for the
		* convergence of the algorithm
		**/
		if ($offset >= $this->_linesOffsets[0][0] and $offset <= $this->_linesOffsets[0][1])
		return 0;

		if ($offset >= $this->_linesOffsets[$size][0] and $offset <= $this->_linesOffsets[$size][1])
		return $size;

		do{
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
	* Opposit to {@see ::getLineByOffset()} returm offset of line begin.
	*
	* @param	integer	$line
	* @return	array(OffsetBeginLine, OffsetEndLine). In OffsetEndLine included length of ->_lineSep!
	**/
	public function getOffsetByLine($line){
		if (!$this->_linesOffsets) $this->makeCacheLineOffsets();
		if ($line >= sizeof($this->_linesOffsets)) throw new VariableRangeException('Overflow! This line does not exists.');

	return $this->_linesOffsets[$line];
	}#m getOffsetByLine

	/**
	* Check loaded content is not empty. Throw exception otherwise.
	*
	* @return	&this
	* @Throw(VariableEmptyException)
	**/
	private function &checkLoad(){
		if (empty($this->lineContent) and empty($this->content))
		throw VariableEmptyException('Line-Content and Content is empty! May be you forgot call one of ->load*() method first?');
	return $this;
	}#m checkLoad

	/**
	* Make cache of lines and its offsets.
	**/
	private function makeCacheLineOffsets(){
	$this->_linesOffsets = array();
	$offset = 0;
	$lines =& $this->getLines();

	$linesCount = sizeof($lines);	#For speed up
	#First line is additional case
	$this->_linesOffsets[0] = array($offset, ($offset += -1 + strlen(utf8_decode($lines[0])) + strlen(utf8_decode($this->getLineSep()))) );
		#From 1 line, NOT 0
		for($i = 1; $i < $linesCount; $i++){
		$this->_linesOffsets[$i] = array(
			$offset + 1,
			( $offset += strlen(utf8_decode($lines[$i])) + strlen(utf8_decode($this->getLineSep())) )  );
		}
	}#m makeCacheLineOffsets
}#c file_inmem
?><?
/**
* RegExp manupulation.
* @package RegExp
* @version 2.1.2.1
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
*
*	* 2009-02-11 13:41 ver 2.1 to 2.1.1
*	- Add method split
*
*	* 2009-03-02 01:52 ver 2.1.1 to 2.1.2
*	- Add optional parameter $n into ::getMatches() method.
*	- Add ::getHuMatches() method
*
*	* 2009-03-02 16:49 ver 2.1.2 to 2.1.2.1
*	- Fix setTextRef, to set ref, not copy :)
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
	* @Throws(VariableIsNullException)
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
	$this->sourceText =& $text;
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
	* Full(os sub, if $n present) array of matches after call (not checked!) {@see doMatch()}, {@see doMatchAll()}, {@see split()}
	*
	* @param	int|null	Number of sub array
	* @return array of last matches.
	**/
	public function getMatches($n = null){
		if (is_null($n)) return $this->matches;
		else return $this->matches[$n];
	}#m getMatches

	/**
	* Full equivalent of {@see getMatches()) except of result returned as Object(HuArray) instead of regular array.
	*
	* @param	int|null	Directly passed to {@see getMatches}
	* @return Object(HuArray) of last matches.
	**/
	public function getHuMatches($n = null){

	return new HuArray($this->getMatches($n));
	}#m getHuMatches
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
?><?
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
<?
/**
* Class to provide OOP interface to array operations.
*
* @package Vars
* @version 1.2.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-09-22 17:55 ver 1.1 to 1.1.1
*	* Add majority this phpdoc header.
*	- Change
*
*	* 2009-02-27 15:08 ver 1.1.1 to 1.1.2
*	- Some minor fixes in comments.
*
*	* 2009-02-27 17:22 ver 1.1.2 to 1.1.3
*	- Add method filter()
*	- Add support and implementation of Iterator interface
*
*	* 2009-03-02 02:04 ver 1.1.3 to 1.1.4
*	- Add method ::implode()
*	- Add metchod ::count()
*
*	* 2009-03-06 15:29 ver 1.1.4 to 1.1.5
*	- Change
*
*	* 2009-03-08 15:31 ver 1.1.5 to 1.2
*	- Add method {@see ::hu()}.
*	- Modified method __get to support construction like: $HuArrayObj->{'hu://varName'}
*	- Add methods ::filterByKeys() and ::filterOutByKeys().
*	- Add method ::filterKeysCallback()
*
*	* 2009-03-10 10:33 ver 1.2 to 1.2.1
*	- In method push implementation add backward compatability with php <= 5.2.9.
**/



class HuArray extends settings implements Iterator{
const huScheme = 'hu://';

	/**
	* Constructor.
	*
	* @param	(array)mixed=null	$array	 Mixed, explicit cast as array!
	**/
	function __construct(/*(array)*/ $array = null){
	parent::__construct((array)$array);
	}#__c

	/**
	* Push values.
	*
	* @param 	mixed	$var.
	* @params	mixed	any amount of vars (First explicity to make mandatory one at once)
	* @return	&$this
	**/
	public function &push($var){
	//On old PHP got errro: PHP Fatal error:  func_get_args(): Can't be used as a function parameter in /home/_SHARED_/Vars/HuArray.php on line 58
	//call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), func_get_args()));
	//Do the same with temp var:
	$args = func_get_args();
	call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), $args));
	return $this;
	}#m push

	/**
	* Push array of values.
	*
	* @param 	array	$arr
	* @return	&$this
	**/
	public function &pushArray(array $arr){
		if ($arr)
		call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), $arr));
	return $this;
	}#m pushArray

	/**
	* Push values from Object(HuArray).
	*
	* @param 	mixed	$var.
	* @return	$this->pushArray()
	**/
	public function &pushHuArray(HuArray $arr){
	return $this->pushArray($arr->getArray());
	}#m pushHuArray

	/**
	* Return last element in array. Reference, direct-editable!!
	*
	* @return &mixed
	**/
	public function &last(){
	end($this->__SETS);
	return $this->__SETS[key($this->__SETS)];
	}#m last

	/**
	* Return Array representation (cast to (array)).
	*
	* @return	array
	**/
	public function getArray(){
	return $this->__SETS;
	}#m getArray

	/**
	* {@see http://php.net/array_slice}
	*
	* @param	integer	$offset
	*	Если параметр offset положителен, последовательность начнётся на расстоянии offset от начала array. Если offset отрицателен, последовательность начнётся на расстоянии offset от конца.
	* @param	integer	$length
	*	Если в эту функцию передан положительный параметр length, последовательность будет включать length элементов. Если в эту функцию передан отрицательный параметр length, в последовательность войдут все элементы исходного массива, начиная с позиции offset и заканчивая позицией, отстоящей на length элементов от конца. Если этот параметр будет опущен, в последовательность войдут все элементы исходного массива, начиная с позиции offset.
	* @param	boolean	$preserve_keys
	*	Обратите внимание, поумолчанию сбрасываются ключи массива. Можно переопределить это поведение, установив параметр preserve_keys в TRUE.
	* @return Object(HuArray)
	**/
	public function getSlice($offset, $length = null, $preserve_keys = false){
	return new HuArray(array_slice($this->__SETS, $offset, EMPTY_VAR($length, sizeof($this->__SETS)), $preserve_keys));
	}#m getSlice

	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	public function &getProperty($name){
	return $this->__SETS[REQUIRED_NOT_NULL($name)];
	}#m getProperty

	/**
	* @var	&mixed	->_last_
	**/
	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	function &__get($name){
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference:
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name) return $this->last();
		/*
		* Short form of ::hu. To allow constructions like:
		* $obj->{'hu://varName'}->{'hu://0'};
		* instead of directly:
		* $obj->hu('varName')->hu(0);
		* As you like
		**/
		elseif( self::huScheme == substr($name, 0, strlen(self::huScheme)) ) return $this->hu( substr($name, strlen(self::huScheme)) );
		else
		return $this->getProperty($name);
	}#m __get

	/**
	* Like standard {@see __get()}, but if returned value is regular array, convert it into HuArray and return reference to it.
	* @example:
	* $ha = new HuArray(
	*	array(
	*		'one' => 1
	*		,'two' => 2
	*		,'arr' => array(0, 11, 22, 777)
	*	)
	* );
	* dump::a($ha->one);
	* dump::a($ha->arr);					// Result Array (raw, as is)!
	* dump::a($ha->hu('arr'));				// Result HuArray (only if result had to be array, as is otherwise)!!! Original modified in place!
	* dump::a($ha->hu('arr')->hu(2));			// Property access. Alse as any HuArray methods like walk(), filter() and any other.
	* dump::a($ha->{'hu://arr'}->{'hu://2'});	// Alternate method ({@see ::__get()}). Fully equivalent of line before. Just another form.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	function &hu($name){
		if (is_array($this->$name)) $this->$name = new HuArray($this->$name);
	return $this->getProperty($name);
	}#m hu

	/**
	* Allow change value by short direct form->setttingName = 'qwerty';
	*
	* @param	string	$name
	* @param	mixed	$value
	**/
	function __set($name, $value){
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference:
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name){
		$ref =& $this->last();
		}
		else{
		$ref =& $this->getProperty($name);
		}
	$ref = $value;
	}#m __set

	/**
	* Apply callback function to each element.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function walk($callback){
	array_walk($this->__SETS, $callback);
	return $this;
	}#m walk

	/**
	* Filter array, using callback. If the callback function returns true, the current value from input is returned into the result
	* array. Array keys are preserved.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function &filter($callback){
	$this->__SETS = array_filter($this->__SETS, $callback);
	return $this;
	}#m filter

	/**
	* Filter array by keys and leave only mentioned in $keys array
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterByKeys(array $keys){
	$this->__SETS = array_diff_key( $this->__SETS, array_flip(  array_intersect(   array_keys($this->__SETS), $keys   )  ) );
	return $this;
	}#m filterByKeys

	/**
	* Filter array by keys and leave only NOT mentioned in $keys array (opposite to method {@see ::filterByKeys()})
	*
	* Implementation idea taken from: http://ru.php.net/array_filter comment of niehztog
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterOutByKeys(array $keys){
	$this->__SETS = array_diff_key( $this->__SETS, array_flip($keys) );
	return $this;
	}#m filterOutByKeys

	/**
	* Similar to {@see ::filer()} except of operate by keys instead of values.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function &filterKeysCallback($callback){
	$keys = new self(array_flip( $this->__SETS ));
	$keys->filter($callback);
	$this->filterByKeys($keys->getArray());
	return $this;
	}#m filterKeysCallback

	/**
	* Implode to the string using provided delimiter.
	*
	* @param	string	$delim
	* @return	string
	**/
	public function implode($delim){
	return implode($delim, $this->__SETS);
	}#m implode

	/**
	* Return number of elements
	*
	* @return	int
	**/
	public function count(){
	return count($this->__SETS);
	}#m count

/*##########################################################
## From interface Iterator
##########################################################*/
	public function rewind(){
	reset($this->__SETS);
	}#m rewind

	public function current(){
	return /* $var = */ current($this->__SETS);
	}#m current

	public function key(){
	return /* $var = */ key($this->__SETS);
	}#m key

	public function next(){
	return /* $var =*/ next($this->__SETS);
	}#m next

	public function valid(){
	return ($this->current() !== false);
	}#m valid
}#c HuArray
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
* @version 1.0.4
*
* @changelog
*	* 2008-05-30 16:08
*	- Add compatibility with PHP < 5.3.0. Replace "static::$properties" to "self::$properties"
*	 in case when it defined and used in one class is it correct.
*
*	* 2008-09-22 17:44 ver 1.0.1 to 1.0.2
*	- Change
*
*	* 2009-03-01 14:55 ver 1.0.2 to 1.0.3
*	- Method checkNamePossible() changed from private to protected (Primarly for Config class)
*
*	* 2009-03-06 15:29 ver 1.0.3 to 1.0.4
*	- Change
*
*	* 2009-03-10 04:24 ver 1.0.4 to 1.0.5
*	- Add method ::addSetting()
**/




/**
* Extended variant of settings, with check possible options.
* Slowly, but safely.
**/
class settings_check extends settings{
static public $properties = array();

	/**
	* Constructor.
	* @param	array	$possibles. Array of string - possibe names of propertys.
	* @param	array=null $array
	**/
	function __construct(array $possibles, array $array = null){
	self::$properties = $possibles;
		if ($array) $this->mergeSettingsArray($array);
	}#constructor

	public function setSetting($name, $value){
	parent::setSetting($this->checkNamePossible($name, __METHOD__), $value);
	}#m setSetting

	public function getProperty($name){
	return parent::getProperty($this->checkNamePossible($name, __METHOD__));
	}#m getProperty

	/**
	* Add setting vith value in possible settings.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	nothing
	**/
	public function addSetting($name, $value){
	self::$properties[] = $name;
	parent::setSetting($name, $value);
	}#m addSetting

	/**
	* ПЕРЕЗАПИСЫВАЕТ ВСЕ настройки. Для изменения отдельных - setSetting
	* Хорошо было бы это все в setSettings запихать, но перегрузка не поддерживается :(. Что ж, будут разные имена.
	**/
	public function setSettingsArray(array $setArr){
	array_walk(array_keys(REQUIRED_VAR($setArr)), array($this, 'checkNamePossible'), __METHOD__);
	parent::setSettingsArray($setArr);
	}#m setSettingsArray

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	* @param	string	$name	Name of required property
	* @return boolean
	*/
	public function __isset($name) {
	return parent::__isset($this->checkNamePossible($name, __METHOD__));
	}#m __isset

	/**
	* ПЕРЕЗАПИСЫВАЕТ УКАЗАННЫЕ настройки. Для изменения отдельных - setSetting
	* Хорошо было бы это все в setSettings запихать, но перегрузка не поддерживается :(. Что ж, будут разные именаю
	**/
	public function mergeSettingsArray(array $setArr){
	array_walk(array_keys(REQUIRED_VAR($setArr)), array($this, 'checkNamePossible'), __METHOD__);
	parent::mergeSettingsArray($setArr);
	}#m mergeSettingsArray

	/**
	* Check if name is possible, and Throw(ClassPropertyNotExistsException) if not.
	* @param	string	$name. Name to check.
	* @param	string	$method. To Exception - caller method name.
	* @param	string	$walkmethod. Only for array_walk compatibility - it is must be 3d parameter.
	* @return	string	$name
	* @Throw(ClassPropertyNotExistsException)
	**/
	protected function checkNamePossible($name, $method, $walkmethod = null){
		if (!in_array($name, self::$properties)) throw new ClassPropertyNotExistsException(EMPTY_STR($walkmethod, $method).': Property "'.$name.'" does NOT exist!');
	return	$name;
	}#m checkNamePossible
}#c settings_check
?><?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Settings
* @version 0.4
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-01 22:12 ver 0.1
*	- Initial version
*
*	* 2009-03-06 15:29 ver 0.1 to 0.2
*	- Change
*
*	* 2009-03-09 05:31 ver 0.2 to 0.3
*	- Add optional parameter $className to CONF() function!
*	- @subpackage changed to Settings
*
*	* 2009-03-10 04:59 ver 0.3 to 0.4
*	- Add try to autoinclude config: 'includes/configs/' . $name . '.config.php' if it is untill not present in $GLOBALS['__CONFIG']
**/

//It used for __autoload, so, we must directly prowide dependencies here


 // OS::is_includable


/**
* Class to provide easy access to $GLOBALS['__CONFIG'] variables.
* Intended use with Singleton class as:
* @example Single::def('HuConfig')->config_value
**/
class HuConfig extends settings_check{
private $_include_tryed = array();
	function __construct() {
	parent::__construct(array_keys($GLOBALS['__CONFIG']), $GLOBALS['__CONFIG']);
	}#__c

	/**
	* As __get before.
	* Now {@see __get()} reimplemented to return HuArray instead of raw arrays
	* Bee careful - after standard call (not raw) original Array value was replaced by HuArray!
	*
	* @param string	$varname
	* @param boolean(false)	$nothrow If true - silently not thrown any exception.
	* @return &mixed
	**/
	public function &getRaw($varname, $nothrow = false){
	return $this->getProperty($varname, $nothrow);
	}#m getRaw

	/**
	* For more comfort access in config fields without temporary variables like:
	* Single::def('HuConfig')->test->first
	*
	* @param string	$varname
	* @return &Object(HuArray)
	**/
	public function &__get($varname){
	$ret =& $this->getProperty($varname);
		if (is_array($ret)){
		$ret = new HuArray($ret); //Replace original on the fly
		return $ret;
		}
		else return $ret;
	}#m __get

	/**
	* Reimplement as initial, only return value by reference
	* Also try include file 'includes/configs/' . $name . '.config.php' if it exist to find needed settings.
	* @inheritdoc
	* @param	boolean(false)	$nothrow If true - silently not thrown any exception.
	**/
	public function &getProperty($name, $nothrow = false){
		try{
		return $this->__SETS[$this->checkNamePossible(REQUIRED_NOT_NULL($name), __METHOD__)];
		}
		catch(ClassPropertyNotExistsException $cpne){
			//Try include appropriate file:
			if (!in_array($name, $this->_include_tryed)){
			$this->_include_tryed[] = $name; //In any case to do not check again next time
			$path = 'includes/configs/' . $name . '.config.php';
//			dump::a($path);dump::a(OS::is_includeable($path));
				if(OS::is_includeable($path)){
				include($path);
					if(m()->is_set($name, $GLOBALS['__CONFIG'])){//New key
					$this->addSetting($name, $GLOBALS['__CONFIG'][$name]);
					}
				//return $this->getProperty($name); //Again
				return $this->__SETS[$name];
				//return $this->__SETS[$this->checkNamePossible(REQUIRED_NOT_NULL($name), __METHOD__)];
				}
			}
			//Silent if required.
			if (!$nothrow) throw $cpne; //If include and fine failed throw outside;
			else{
			// Avoid: Notice: Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 101
			$t = null;
			return $t;
			}
		}
	}#m getProperty
}#c

/**
* Short alias to Single::def('config'). In case of we can-t define constant like:
* define('CONF', Single::def('config'));
* In this case got error: PHP Warning:  Constants may only evaluate to scalar values
* We can do that as variable like $CONF, but meantime it is not convenient in functions/methods:
* we must use global $CONF; first, or also very long $GLOBALS['CONF']
*
* So, choose function aliasing. Now we can invoke it instead of Single::def('HuConfig')->config_value
* or even $GLOBALS['CONF']->someSetting but just:
* CONF()->config_value
*
* Furthermore most often use of that will: Single::def('HuConfig')->className->setting.
* So, class name put to optioal parameter to allow like:
* CONF('className')->desiredClassOption
*
* @param	string(null)	$className Optional class name
* @param	boolean(false)	$nothrow If true - silently not thrown any exception.
* @return Single_Object(HuConfig)|Object(HuArray). If className present - Object(HuArray) returned, Single_Object(HuConfig) otherwise to next query.
**/
function &CONF($className = null, $nothrow = false){
	/*
	* Strange, but if we direct return:
	* if ($className) return Single::def('HuConfig')->$className;
	* All work as expected and variable returned by reference, but notice occured:
	* PHP Notice:  Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 111
	* implicit call to __get solve problem. Is it bug?
	* @todo Fill bug
	**/
	/*
	* We want use HuConfig in singleton::def. It is produce cycle dependency.
	* So, rely on HuConfig do not take any settings in constructor, we may sefely call Single::singleton directly
 	if ($className) return Single::def('HuConfig')->__get($className);
	else return Single::def('HuConfig');
	**/
	if ($className) return Single::singleton('HuConfig')->__get($className);
	else return Single::singleton('HuConfig');
}#f CONF

/**
* @example
* dump::a(Single::def('HuConfig')->test);
* dump::a(Single::def('HuConfig')->test->First);
* dump::a(Single::def('HuConfig')->test->Second);
* Single::def('HuConfig')->test->Second = 'Another text';
* dump::a(Single::def('HuConfig')->test->Second);
* CONF()->test->Second = 'Yet ANOTHER Another text';
* dump::a(CONF()->test->Second);
* dump::a(Single::def('HuConfig')->test);
**/
?><?
/**
* Singleton pattern.
*
* @package Vars
* @subpackage Classes
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-30 13:22
*	- Fore backward capability replace construction (!@var ?: "Error") to (!@var ? '' : "Error")
*
*	* 2009-03-01 11:07 ver 1.0b to 1.1
*	- Method tryIncludeByClassName now deprecated. Use autoload instead.
*	- In ::def() method suppress error if config absent: @$GLOBALS['__CONFIG'][$className]
*	- Method def now return reference, how it do method singleton too.
*	- Add few examples of usage.
*
*	* 2009-03-05 13:18 ver 1.1 to 1.1.1
*	- fprintf(STDERR, ...) replaced to file_put_contents('php://stderr', ...) to do not fire warnings what STDERR defined when in web.
*
*	* 2009-03-05 13:39 ver 1.1.1 to 1.1.2
*	* Adjust include, since OS::is_includeable now only return boolean, do not tryed include anything.

*	* 2009-03-10 06:19 ver 1.1.2 to 1.2
*	- Method ::def() now used CONF()->getRaw($className) instead of direct accessing to @$GLOBALS['__CONFIG'][$className]
*		with all futures what it does such as settings autoload.
*	- include_once('Vars/HuConfig.class.php') added additional dependency.
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
	* The main singleton static method
	* All call must be: Single::singleton('ClassName'). Or by its short alias: Single::def('ClassName')
	*
	* @param	string	$className Class name to provide Singleton instance for it.
	* @params variable number of parameters. Any other parameters directly passed to instantiated class-constructor.
	**/
	public static function &singleton($className){
		if (!isset(self::$instance[$className])){// @TODO: provide hashing class name and args, and index by hash.
			if (!function_exists('__autoload')) self::tryIncludeByClassName($className);

		$args = func_get_args();
		unset($args[0]);//Class name
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
	*
	* @return &Object($classname)
	**/
	public static function &def($className){
	//return self::singleton($className, @$GLOBALS['__CONFIG'][$className]);
	return self::singleton($className, CONF()->getRaw($className, true));
	}#m def

	/**
	* Try include
	* @deprecated Use autoload instead.
	*
	*
	* @param string	$className Name of needed class
	* @return
	**/
	public static function tryIncludeByClassName($className){
	file_put_contents('php://stderr', 'Usage of Single::tryIncludeByClassName is deprecated. Use autoload instead.');
		#is_readable is not use include_path, so can not use this check. More explanation see {$link OS::is_includeable()}
		if (!class_exists($className) and isset($GLOBALS['__CONFIG'][$className]['class_file']) and OS::is_includeable($GLOBALS['__CONFIG'][$className]['class_file']))
		include($GLOBALS['__CONFIG'][$className]['class_file']);

		#repetition check
		if (!class_exists($className)) throw new ClassNotExistsException($className . ' NOT exist!'. (!@$GLOBALS['__CONFIG'][$className]['class_file'] ? '' : ' And, additionaly include provided path ['.$GLOBALS['__CONFIG'][$className]['class_file'].'] not helped in this!'));
	}#m tryIncludeByClassName

	/**
	* Prevent users to clone the instance
	**/
	public function __clone(){
	trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
}#c Single

/**
* @example
* This will always retrieve a single instance of the class
*
* $test = Single::singleton();
* $test->bark();
* $test = Single::singleton()->bark();
* //Default invoke, using $GLOBALS['__CONFIG']['classname'] as arguments.
* Single::def('classname')->...
**/
?><?
/**
* In PHP we unfortunately do not have multiple inheritance :(
* So, turn it class into interface and provide common, possible implementation
* through static methods of __outExtraData__common_implementation homonymous methods
* and providing link to $this and in method implementation refer to it as &obj instead of direct $this.
*
* Common implementation will be present in comments near after declaration.
**/

interface outExtraData{
//public $_curTypeOut = OS::OUT_TYPE_BROWSER; //Track to helpers, who provide format (parts) and need known for what
	/**
	* String to print into file. Primary for logs string representation
	*
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToFile($format = null);

	/**
	* Return string to print into user browser.
	*
	* @param * @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToWeb($format = null);

	/**
	* String to print on console.
	*
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToConsole($format = null);

	/**
	* String to print. Automaticaly detect (by {@link OS::getOutType()}) Web or Console and
	*	invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = null);/*{Now common solution is (see description on begin abput Multiple Inheritance):
	return __outExtraData__common_implementation::strToPrint($this, $format);
	}#m strToPrint
	*/

	/**
	* Convert to string by provided type.
	*
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = null);/*{Now common solution is (see description on begin abput Multiple Inheritance):
	return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	*/
}#c

/* see description on begin about Multiple Inheritance **/
class __outExtraData__common_implementation{
	//Only hack - common realization!
	public static function strByOutType(/*$this*/&$obj, $type, $format = null){
	$obj->_curTypeOut = $type;
		switch ($type){
		case OS::OUT_TYPE_BROWSER:
		return $obj->strToWeb($format);
		break;

		case OS::OUT_TYPE_CONSOLE:
		return $obj->strToConsole($format);
		break;

		case OS::OUT_TYPE_FILE:
		return $obj->strToFile($format);
		break;

		#Addition, preudo
		case OS::OUT_TYPE_PRINT:
		return $obj->strToPrint($format);
		break;

		default:
		throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m strByOutType

	public function strToPrint(/*$this*/&$obj, $format = null){
	$obj->_curTypeOut = OS::OUT_TYPE_PRINT;//Pseudo. Will be clarified.
		if (OS::OUT_TYPE_BROWSER == OS::getOutType()) return $obj->strToWeb($format);
		else return $obj->strToConsole($format);
	}#m strToPrint
}#c
?><?
/**
* System environment and information
* @package System ??
* @version 2.0.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
*	@changelog
*	* 2008-11-05 00:47 ver 2.0b to 2.0.1
*	- In method OS::is_includeable() remove second parameter $include, because including file in caller context
*		is not possible. And inclusion in context of this method is mistake!
*
*	* 2009-01-25 00:58 ver 2.0.1 to 2.0.2
*	- Add method isPathAbsolute()

*	* 2009-02-26 15:56 ver 2.0.2 to 2.0.3
*	- In method isPathAbsolute($pathToCheck) add handling registered wrappers to always absolute!
**/

/**
* Class OS has mainly (all) static methods, to determine system-enveroments, like OS or type of out.
* Was System, but it is registered in PEAR, change to OS
**/
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
**/
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
	*
	* @return Now one of const: ::OUT_TYPE_BROWSER or ::OUT_TYPE_CONSOLE
	**/
	static public function getOutType(){
		if (isset($_SERVER['HTTP_USER_AGENT'])) return self::OUT_TYPE_BROWSER;
		else return self::OUT_TYPE_CONSOLE;
	}#m getOutType

	/**
	* php_sapi_name()
	*
	* @return
	**/
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
	*
	* @param	string $filenam As it can be passed to include or require.
	* @return
	**/
	static public function is_includeable($filename){
		/** is_file, is_readable not suitable, because include_path do not take effect.
		* And opposite comment of "php at metagg dot com" and "medhefgo at googlemail dot com",
		* woudn't manualy check all paths in include_path. Just open this file to read
		* with include_path check parameter support! */
		if ($res = @fopen($filename, 'r', true)){
		fclose($res);	// Not realy need opened file, only result of opening.
		}
	return (bool)$res;
	}#m is_inludeable

	/**
	* Check if given path is absolute or not.
	*
	* @param $pathToCheck	string Path to check
	* @return boolean
	**/
	static public function isPathAbsolute($pathToCheck){
		if ( preg_match('#^(?:' . implode('|', stream_get_wrappers()) . ')://#', $pathToCheck) ) return true; // Registered wrappers always absolute!

		//@TODO: case 'DAR': ;break; //Darwin http://qaix.com/php-web-programming/139-944-constant-php-os-and-mac-server-read.shtml
		// This check from http://ru2.php.net/php_uname
		if ('WIN' != strtoupper(substr(PHP_OS, 0, 3))){
		return ( '/' == $pathToCheck{0} );
		}
		else{//WIN
		return ( ':' == $pathToCheck{1} );
		}
	}
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
* @version 1.4
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	*2008-05-31 5:31 v 1.0b to 1.0c
*	- Add static method ::createWithoutLSB.
*
*	*2008-06-05 16:00 v 1.0c to 1.1
*	- In function classCREATE provide all aditions arguments to HuClass::createWithoutLSB
*
*	* 2009-01-18 13:13 ver 1.1 to 1.2
*	- Rename file from Class.php to HuClass.php
*
*	* 2009-02-27 15:23 ver 1.2 to 1.3
*	- Make parameter $directClassName mandatory in ::createWithoutLSB()
*	- and all logic to search name moved from it into ::create()!
*	- Fix function classCREATE, make $ClassName parameter mandatory
*
*	* 2009-03-08 13:33 ver 1.3 to 1.4
*	- To break loop: Vars/Settings/settings.php:24 -> Vars/HuClass.php:28 -> macroses/REQUIRED_VAR.php:16 ->
*		-> Exceptions/variables.php:93 -> Debug/backtrace.php:45 -> Debug/HuFormat.php:14 -> Vars/Settings/settings.php
*		 moved to method createWithoutLSB (it single who uses this macro)
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
	*
	* @param variable parameters according to class.
	* @return instance of the reguired new class.
	* @Throw(ClassUnknownException)
	**/
	static function create(){
//	$reflectionObj = new ReflectionClass(static::className);
	#http://blog.felho.hu/what-is-new-in-php-53-part-2-late-static-binding.html
		if (function_exists('get_called_class')) $className = get_called_class(); # Most reliable if available
//??Possible??		elseif(isset(self::_CLASS_)) $className = self::_CLASS_; # Fallback to emulate if present
		else throw new ClassUnknownException('Can\'t determinate class name for eho is called ::create() (LSB is not accesible [present start from PHP 5.3.0-dev]). You can use ::createWithoutLSB method or classCREATE() free function with explicit name of needed class!');
	$reflectionObj = new ReflectionClass($className);

		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(func_get_args());
		else return $reflectionObj->newInstance();
	}#m create

	/**
	* This is similar create, but created for backward capability only.
	* It is UGLY. Do not use it, if you have choice.
	* It is DEPRECATED immediately after creation! But now, realy, it is stil neded :(
	*
	* @deprecated
	* @param $directClassName = null - The directy provided class name to instantiate.
	* @params variable parameters according to class.
	* @return instance of the reguired new class.
	* @Throw(VariableRequired)
	**/
	static function createWithoutLSB($directClassName /*, Other Params */){

	$reflectionObj = new ReflectionClass(REQUIRED_VAR($directClassName));
	$args = func_get_args();//0 argument - $directClassName
		// use Reflection to create a new instance, using the array of args
		if ($reflectionObj->getConstructor()) return $reflectionObj->newInstanceArgs(array_slice($args, 1));
		else return $reflectionObj->newInstance();
	}#m createWhithoutLSB

	/**
	* PHP hasn't any normal possibilities to cast objects into derived class. We need hack to do it.
	* See http://ru2.php.net/mysql_fetch_object comments by "Chris at r3i dot it"
	* So, in this page, below, i found next fine workaraound (see comment and example of "trithaithus at tibiahumor dot net")
	*
	* Also this hack was be founded here http://blog.adaniels.nl/articles/a-dark-corner-of-php-class-casting/
	* @param $toClassName string Class name to what casting do
	* @param $what mixed
	* @return Object($toClassName)
	**/
	static function cast($toClassName, $what){
	return unserialize(
			preg_replace(
				'/^O:[0-9]+:"[^"]+":/',
				'O:'.strlen($toClassName).':"' . $toClassName . '":',
				serialize($what)
			)
		);
	}#m cast
}#c HuClass

/**
* Free function. For instantiate all objects.
* {@inheritdoc HuClass::createWithoutLSB}
**/
function classCREATE($ClassName /*, Other Params */){
/*
* We must use temporary variable due to error:
* PHP Fatal error:  func_get_args(): Can't be used as a function parameter in /home/_SHARED_/Vars/HuClass.php on line 107
**/
$args = func_get_args(); //0 argument - $ClassName
return call_user_func_array(
	array(
		'HuClass',
		'createWithoutLSB'
	)
	,$args
);
}
?><?
/**
* Toolkit of small functions as "macroses".
*
* @package Macroses
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-13 12:18 ver 1.0 to 2.0
*	- Add PhpDoc to NON_EMPTY_STR macros
*	- Make check on "NON EMPTY" in EMPTY_STR macros more complexity then
*		just "if (@$str)" was in version 1.0. (see in doc below more detailse).
*	- Macros NON_EMPTY_STR now use NON_EMPTY one, for the more complexity provided checks (see before)
*	- Add example and test file and @example tag into both functions.
**/

/**
* Return first NON-empty string if present. Silent return empty string "" otherwise.
*
* WARNING! This macros operate by *strings*. In particular case it means are:
*	1) What null/false and even *TRUE* values threated as EMPTY *STRINGS* and default
*		value will be returned!
*	2) Opposite it, integer 0 failse this check end go to default value, what it also
*		is not what was prefered. We handle "0" correctly too as "NON EMPTY STRING"
*	3) Macros do not intended to use with arrays, but PHP has internal support conversion its
*		to 'Array' string. It is usefull. BUT, nevertheless unfortunately empty
*		array() converted into empty string! To cast into single form, all arrays
*		converted into string like "Array(N)" where N is count of elements.
*
* @example EMPTY_STR.example.php
*
* @params	variable amount of arguments.
* @return	string
**/
function EMPTY_STR(){
$numargs = func_num_args();
$i = 0;
$str = null;
	do{
	$str = func_get_arg($i++);
	}
	while (
		!(//Most comples check. See explanation in PhpDoc
			(//It must be first check, because non-empty array simple check evaluated into true.
				is_array($str) //Explicit check, even it is EMPTY array
				and
				($str = 'Array(' . count($str) . ')')	# Assign in condition
			)
			or
			(
				true === $str	# False and null values self converted to empty string and do not require futher checks
				and
					(
					# Assign in condition and explicitly return true, because '' is false as empty string
					$str = ''
					or
					true
					)
			)
			or
			0 === $str		# Integer 0 is string "0" but evaluated in empty by previous check
			or
			$str				# Last generick check after all special cases!
		)
		and
		$i < $numargs //In do-wile it must be last
	);
return (string)$str;
}#f EMPTY_STR

/**
* If provided argument $str is not empty *string* then return "$prefix.$str.$suffix" otherwise $defValue
*
* WARNING! this macros operate by *STRINGS*, so, it is handle several values such as 0, true, Array() by special way.
* To determine of string "empting" it is fully relyed on {@see EMPTY_STR()}. Please se it for more details.
*
* @example EMPTY_STR.example.php
*
* @param	string $str
* @param	string $prefix
* @param	string $suffix
* @param	string $defValue
* @return	string
**/
function NON_EMPTY_STR(&$str, $prefix='', $suffix='', $defValue=''){
// strlen because '0'? treated as false and default value returned
return ( strlen(($str = EMPTY_STR($str))) > 0 ? $prefix.$str.$suffix : $defValue);
}#f NON_EMPTY_STR
?><?
/**
* Provide easy to use settigns-cllass for many purpose. Similar array
* of settings, but provide several addition methods, and magick methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see
* class HuFormat}).
*
* @package Vars
* @subpackage settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-30 23:19
*	- Move include macroses REQUIRED_VAR.php and REQUIRED_NOT_NULL.php after declaration class
*	 settings to break cycle of includes
*
*	* 2009-01-18 14:57 (No version bump)
*	- Reflect renaming Class.php to HuClass.php
**/


	#Static method ::create()

class settings extends HuClass{
protected $__SETS = array();#Сами настройки, массив

	/**
	* Constructor.
	*
	* @param array=null $array
	**/
	function __construct(array $array = null){
		if ($array) $this->mergeSettingsArray($array);
	}#__c

	/**
	* Set setting by its name.
	*
	* @param	string	$name
	* @param	mixed	$value
	**/
	public function setSetting($name, $value){
	$this->__SETS[$name] = $value;
	}#m setSetting

	/**
	* Rewrite ALL settings. To change only needed - use {@see ::setSetting()} method
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	* @return	nothing
	**/
	public function setSettingsArray(array $setArr){
	$this->__SETS = REQUIRED_VAR($setArr);
	}#m setSettingsArray

	/**
	* Rewrite provided settings by its values. To change single setting you may use {@see ::setSetting()}
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	**/
	public function mergeSettingsArray(array $setArr){
	$this->__SETS = array_merge((array)$this->__SETS, REQUIRED_VAR($setArr));
	}#m mergeSettingsArray

	/**
	* Return requested property by name. For more usefull access see {@see ::__get()} method.
	*
	* @param	string	$name
	* @return	mixed
	**/
	public function getProperty($name){
	return ($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}#m getProperty

	/**
	* Usefull alias of {@see ::getProperty()} to provide easy access in style of $obj->PropertyName
	*
	* @param	string	$name
	* @return	mixed
	**/
	function __get($name){
	return $this->getProperty($name);
	}#m __get

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of requested property
	* @return	boolean
	**/
	public function __isset($name) {
	return isset($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}#m __isset

	/**
	* Rreturn string in what merged settings by provided format.
	*
	* Descriptiopn of elements $fields {@see ::formatField()} method
	*
	* @param	array	$fields
	* @return	string
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
	*
	* @param	array|string	$field
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
	*
	* @return &$this
	**/
	public function &clear(){
	$this->__SETS = array();
	return $this;
	}#m clear

	/**
	* Return amount of settings.
	*
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
/**
* Parent class for more usefull using in parents who want be "customizable"
**/
class get_settings{
/** WARNING! Must be inicialised in parents! **/
protected /* settings */ $_sets = null;

	/**
	* Overload to provide ref on settings object. So, settings will be changable,
	* but can't be replaced settings object!
	*
	* @param <type> $name
	* @return	mixed
	**/
	public function &__get ($name){
		if ('settings' == $name) return $this->_sets;
	}#m __get

	/**
	* Return settings object
	*
	* @return	&Object(settings)
	**/
	public function &sets(){
	return $this->_sets;
	}#m sets
}#c get_settings<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.1.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-31 03:19
*	- Add capability to PHP < 5.3.0-dev:
*		* Replace construction ($var ?: "text") with macros EMPTY_STR
*
*	* 2008-05-25 17:26
*	- Change
*
*	* 2009-03-05 10:32 ver 2.0b to 2.0
*	- Reformat all PHPdocs
*
*	* 2009-03-05 20:46 ver 2.0 to 2.1
*	- HuError now implements outExtraData.
*	- In all methods default value of $formar changed from '' to null (according to interface)
*	- Implementation of ::strByOutType() and ::strToPrint() moved to "interface common implementation"
*		(see Multiple Inheritance restrictions in it)
*	- Delete now unused ExtraData class. Instead it implemented (in separate file) commonOutExtraData.
*
*	* 2009-03-06 15:29 ver 2.1.2 to 2.1.3
*	- Change
**/








class HuError_settings extends settings{
#Defaults
protected $__SETS = array(
	/**
	* @example HuLOG.php
	**/
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

class HuError extends settings implements outExtraData{
/** Self settings. **/
protected /* settings */ $_sets = null;
public $_curTypeOut = OS::OUT_TYPE_BROWSER; //Track to helpers, who provide format (parts) and need known for what

	public function __construct(HuError_settings $sets = null){
	$this->_sets = EMPTY_VAR($sets, new HuError_settings);
	}#m __construct

	/**
	* Due to absent mutiple inheritance in PHP, just copy/paste from class get_settings.
	* Overloading to provide ref on settings without change possibility.
	* In this case change settings is allowed, but change full settings object - not!
	*
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
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_FILE setting used.
	*	And if it settings empty (or not exists) too, just using dump::log() for all filled fields.
	* @return string
	**/
	public function strToFile($format = null){
	$this->_curTypeOut = OS::OUT_TYPE_FILE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_FILE)) return $this->getString($format);
		else return dump::log($this->__SETS, null, true);
	}#m strToFile

	/**
	* String to print into user browser.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_WEB setting used.
	*	And if it settings empty (or not exists) too, just using dump::w() for all filled fields.
	* @return string
	**/
	public function strToWeb($format = null){
	$this->_curTypeOut = OS::OUT_TYPE_BROWSER;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_WEB)) return $this->getString($format);
		else return dump::w($this->__SETS, null, true);
	}#m strToWeb

	/**
	* String to print on console.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_CONSOLE setting used.
	*	And if it settings empty (or not exists) too, just using dump::c() for all filled fields.
	* @return string
	**/
	public function strToConsole($format = null){
	$this->_curTypeOut = OS::OUT_TYPE_CONSOLE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_CONSOLE)) return $this->getString($format);
		else return dump::c($this->__SETS, null, true);
	}#m strToConsole

	/**
	* String to print. Automaticaly detect Web or Console. Detect by {@link OS::getOutType()}
	*	and invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = null){
	return __outExtraData__common_implementation::strToPrint($this, $format);
	}#m strToPrint

	/**
	* Convert to string by type.
	*
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = null){
	return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	}#m strByOutType

	/**
	* Detect appropriate print (to Web or Console) and return correct form
	*
	* @return string ::strToPrint()
	**/
	public function __toString(){
	return $this->strToPrint();
	}#m __toString

	/**
	* Overload settings::setSetting() to handle autodate
	*
	* @inheritdoc
	**/
	public function setSetting($name, $value){
	parent::setSetting($name, $value);

	$this->updateDate();
	}#m setSetting

	/**
	* Overload settings::setSettingsArray() to handle autodate
	*
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
	*
	* @param	$setArr
	* @return mixed	::setSettingsArray()
	**/
	public function setFromArray(array $setArr){
	return $this->setSettingsArray($setArr);
	}#m setFromArray

	/**
	* Overload settings::mergeSettingsArray() to handle autodate
	*
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
	#Insert BEFORE update data in merge. User data 'date' must overwrite auto, if present!
	$this->updateDate();

	parent::mergeSettingsArray($setArr);
	}#m mergeSettingsArray

	/**
	* Just alias for ::mergeSettingsArray()
	*
	* @param	$setArr
	* @return mixed	::mergeSettingsArray()
	**/
	public function mergeFromArray(array $setArr){
	$this->mergeSettingsArray($setArr);
	}#m mergeFromArray

	/**
	* If settings->AUTO_DATE == true and settings->DATE_FORMAT correctly provided - update current
	* date in ->date
	*
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
	*
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

		if ($fieldValue instanceof outExtraData){
		return NON_EMPTY_STR($fieldValue->strByOutType($this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		elseif($fieldValue instanceof backtrace){
		return NON_EMPTY_STR($fieldValue->printout(true, null, $this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		else return NON_EMPTY_STR($fieldValue, @$field[1], @$field[2], @$field[3]);
	}#m formatField
}#c HuError
?><?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuFormat
* @version 2.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-13 19:01 ver 2.0b to 2.1
*	- Add mod_k (k modifier) and support infrastrukture for it, such as save it acsorr mod_A and mod_I.
*
*	* 2009-03-16 17:28 ver 2.1 to 2.1.1
*	- Method ::parseInputArray() renamed to ::setFormat()
*	- As we averload getString() without arguments, implementation methods
*		strToFile, strToWeb, strToConsole, strToPrint, strByOutType from HuError
*		is not suitable. So, overload it as and thown exception (class by autoload) to avoid accidentally usages.
*
*	* 2009-03-17 12:56 ver 2.1.1
*	- Add @example HuFormat.example.php
**/












class HuFormatException extends VariableException{}

/**
* Class to format different structures.
* @example HuFormat.example.php
**/
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
	*	modifiers must used double letters.
	*	For example:
	*		Mod 'e' => mod_e
	*		Mod 'E' => mod_EE (same as mod_ee)
	*
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
		'k'	=> 512,	#Key. Get key of current iteration of I:::.
	);

	private $_format;				#Array of format.
	private $_modStr;				#Modifiers.
	private $_mod;					#Integer of present mods
	private $_modArr = array();		#Array of present mods
	private $_value;				#Value, what processed in this formating.
	private $_realValue;			#If modified (part) in mod_s, mod_a
	private $_realValued = false;		#Flag, to allow pipe through several mods (like as s. a, e)
	private $_name;
	private $_key;					#Key from mod_I itaration for the mod_k

	private $_resStr;				#For caching

	/**
	* @method Object(settings) sets() return current settings
	**/

	/**
	* @method Object(HuFormat) create() Return new instance of object.
	**/

	/**
	* Constructor
	*
	* {@see ::set()}
	*	Be careful - you should explicit provide value like false (invoke as __construct(null, $t = false) for example, because 2d parameter is reference). Otherwise default value null means - using $this as value!
	**/
	public function __construct(array $format = null, &$value = null, $key = null){
	$this->set($format, $value, $key);
/*
	//Unfortunately a can not Use multiple inheritance. Inherit get_settings is more graceful way.
	runkit_method_copy(__CLASS__, 'sets', 'get_settings', 'sets');
	runkit_method_copy(__CLASS__, 'create', 'Single', 'create');
*/
	}#m __construct

	/**
	* Set main: format and value.
	*
	* @param	array|string	$format. If === null, skipped to allow set other
	*	parts. To clear you may use false/true or any else such as empty string.
	* @param	&mixed	$value.	{@see ::setValue()} Skiped if === null. You
	*	may call {@see ::setValue()} to do that
	* @param	mixed	$key	Key of iteration in mod_I and/or mod_A.
	* @return	&$this
	**/
	public function &set($format = null, &$value = null, $key = null){
		if (null !== $value) $this->setValue($value);
		if (null !== $format) $this->setFormat($format);
	$this->_key = $key;
	return $this;
	}#m set

	/**
	* Return current value.
	*
	* @return &mixed
	**/
	public function &getValue(){
		if ($this->_realValued) return $this->_realValue;
		else return $this->_value;
	}#m getValue

	/**
	* Set value
	*
	* @param	&mixed	$value.	Value to format.
	*	If === null $this->_value =& $this; $this->_realValue =& $this->_value;
	* @return &$this
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
	* @param array|string	$format to parse
	* @return &$this
	**/
	public function &setFormat($format){
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
	}#m setFormat

	/**
	* Parses and set from given str. As separator used {@see self::mods_separator}.
	* F.e.: 'AI:::line'. If separator not present - whole string in NAME!
	*
	* @param string $str
	* @return &$this
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
	*
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

			//If all mod_* are only evaluate value and not produce out.
			if (!$this->_resStr) return $this->getValue();
		}

	return $this->_resStr;
	}#m getString

	/**
	* Set or not?
	*
	* @param integer	$mod.
	* @return boolean
	**/
	public function isMod($mod){
		if (!$this->_mod and $this->_modstr) $this->parseMods();
	return ($this->_mod & $mod);
	}#m isMod

	/**
	* Set, or unset mods.
	*
	* @param string	$mods. String to set o unset Mods like: '-I+s+n'.
	*	If '-' - unset.
	*	If '+' - set.
	*	If '*' - invert.
	*	If absent - equal to '+'
	* @return &$this
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
	*
	* @param string	$modstr	String of modifiers.
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
	*
	* @return string
	**/
	public function &getModsStr(){
	return implode('', $this->_modArr);
	}#m getModsStr

	/**
	* Get Modifiers.
	*
	* @return integer
	**/
	public function &getMods(){
	return $this->_mod;
	}#m setMods

	/**
	* Set Modifiers.
	*
	* @param integer	$mods. Modifiers to set.
	* @return &$this
	**/
	public function &setMods($mods){
	$this->_mod &= $mods;
	$this->parseMods(false);
	return $this;
	}#m setMods

	/**##########################################################
	* Private and Protected methods
	##########################################################**/

	/**
	* Parse modifiers from string. 1 char on mod.
	*
	* @param bolean(true)	$direction
	*	True	- from string $this->_modStr
	*	False	- from integer $this-_mod
	* @return &this
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
	*
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
	*
	* @return void
	**/
	protected function mod_a(){
		if (!$this->_realValued){
		$this->_realValue = $this->_value[$this->_name];
		$this->_realValued = true;
		}
		else $this->_realValue = $this->_value[$this->_realValue];
	}#m mod_a

	/**
	* Process ->_value through NON_EMPTY_STR. ->_format must have appropriate values.
	*
	* @return string
	**/
	protected function mod_n(){
	return NON_EMPTY_STR($this->getValue(), @$this->_format[0], @$this->_format[1], @$this->_format[2]);
	}#m mod_n

	/**
	* Procces ->_value through standard sprintf function. All elements self::sprintf_var (def: __vAr__) in ->_format replaced by its
	* real value, and this array go in sprintf
	*
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
	*
	* @return void
	**/
	protected function mod_e(){
		if (!$this->_realValued){
		eval('$this->_realValue = '.$this->_name.';');
		$this->_realValued = true;
		}
		else eval('$this->_realValue = '.$this->_realValue.';');
	}#m mod_e

	/**
	* Evaluate full! Evaluate all as full result.
	*
	* @return string
	**/
	protected function mod_EE(){
	${self::evalute_var} = $this->getValue();
	eval('$ret = '.$this->_format[0].';');
	return $ret;
	}#m mod_E

	/**
	* Value instead name
	*
	* @return void
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
	*
	* @return string
	**/
	protected function mod_AA(){
	$hf = new self(null, $this->_value, $this->_key);
	$ret = '';
		foreach ($this->_format as $f){
		$hf->setFormat($f);
		$ret .= $hf->getString();
		}
	return $ret;
	}#m mod_AA

	/**
	* Iterate by ->_value or ->_realValue.
	*
	* @return string
	**/
	protected function mod_II(){
	$hf = new self($this->_format, $t = false, $this->_key);
	$ret = '';

		foreach ($this->getValue() as $key => $v){
		$hf->setValue($v);
		$hf->_key = $key; //Only for I usefull
		$ret .= $hf->getString();
		}
	return $ret;
	}#m mod_II

	/**
	* Get Key of cunrrent iteration of I:::.
	*
	* @return string
	**/
	protected function mod_k(){
	$this->_realValue = $this->_key;
	$this->_realValued = true;
	}#m mod_k

	/**
	* As we averload getString() without arguments, implementation from HuError
	* is not suitable. So, overload it as and thown exception (class by autoload) to avoid accidentally usages.
	* @TODO It is very usefull methods. Consider implementation in the future.
	**/
	public function strToFile($format = null){ throw new ClassMethodExistsException('Method strToFile is not exists yet'); }
	public function strToWeb($format = null){ throw new ClassMethodExistsException('Method strToWeb is not exists yet'); }
	public function strToConsole($format = null){ throw new ClassMethodExistsException('Method strToConsole is not exists yet'); }
	public function strToPrint($format = null){ throw new ClassMethodExistsException('Method strToPrint is not exists yet'); }
	public function strByOutType($type, $format = null){ throw new ClassMethodExistsException('Method strByOutType is not exists yet'); }
};#c HuFormat
?><?
/**
* ClassExceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
*
* @changelog
*	* 2008-05-31 5:26 v 1.0 to 1.1
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
*
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
* 	* 2008-05-29 17:51 ver 2.0b to 2.1
*	- Fully rewritten and now contructor of VariableRequiredException takes 1st argument backtrace nor Tokenizer!
*	- Added methods VariableRequiredException: ::varName and ::getTokenizer
*
*	* 2008-05-30 23:19
*	- Move include of Debug/backtrace.php after declaration class VariableRequiredException to
*		break cycle of includes
*
*	* 2009-03-08 11:27 ver 2.1 to 2.2
*	- In varName method, $this->bt->current() replaced by direct $this->bt->getNode(0).
*		In case of object used before (f.e. printout() or any else) 0 element may be not current!!
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
	* Get Tokenizer object, suited to backtrace with instantiated exception.
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
				$this->bt->getNode(0)
			)->parseCallArgs();
		}

	return $this->tok_;
	}
};

class VariableEmptyException		extends VariableRequiredException{}
class VariableIsNullException		extends VariableRequiredException{}

class VariableRangeException		extends VariableException{}
/** Greater than */
class VariableRangeGTException	extends VariableRangeException{}
/** Less than */
class VariableRangeLTException	extends VariableRangeException{}

class VariableArrayInconsistentException extends VariableException{}

/**
* It's Before declaration of VariableRequiredException may produce cycle of includes...
**/

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
* @Throws(VariableRequiredException)
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
*
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
?><?
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
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-18 17:44 ver 2.1 to 2.1.1
*	- Make direct call to $this->_regexp->convertOffsetToChars();. It is not called any time cince RegExp_pcre ver 2.2
*
*	* 2009-03-25 15:03 ver 2.1.1 to 2.1.2
*	- After split file_base to 2 childs switch there use file_inmem.
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
	*
	* @param array|Object(backtraceNode) $db	Array, one of is subarrays from return result by debug_backtrace();
	* @return $this
	* @Throws(VariableRequiredException)
	**/
	public function __construct(/* array | backtraceNode */ $db = array()){
		if (is_array($db)) $this->setFromBTN(new backtraceNode($db));
		$this->setFromBTN($db);
	}#m __construct

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
	**/
	protected function findTextCall(){
	$this->_filePhpSrc = new file_inmem(REQUIRED_VAR($this->_debugBacktrace->file));
	$this->_filePhpSrc->loadContent();

	$rega = '/'
		.RegExp_pcre::quote(@$this->_debugBacktrace->type)	#For classes '->' or '::'. For regular functions not exist.
		.'\b'.$this->_debugBacktrace->function	#In case of method and regular function same name present.
		.'\s*\((.*?)\s*\)'	#call
		.'/xms';
	//c_dump($rega, '$rega');

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
	return $this;
	}#m findCallStrings

	/**
	* Parse tokens
	*
	* @return &$this
	**/
	public function &parseTokens(){
		if (!$this->_callText) $this->findCallStrings();
	//c_dump($this->_callText, '$this->_callText');
	#Without start and end tags not parsed properly.
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
	$sParenthesis = 0;	#stack
	$sz = sizeof($this->_tokens);	#Speed Up
		while ($this->_curTokPos < $sz){
		$token =& $this->_tokens[$this->_curTokPos++];

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
	*
	* @return $this
	**/
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
?><?
/**
* Toolkit of small functions as "macroses".
*
* @package Macroses
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-01-30 15:10 ver 1.0
*	- Initial version
*
*	* 2009-03-01 13:59 ver 1.0 to 1.1
*	- Add second, "safe" variant of macros IS_SET_VAR. It is based on self
*		{@see is_set} instead of standard isset. See overall description why in it.
*	- Old ISSET_VAR can't be switched on IS_SET, because use 1 incoming parameter.
*		Also, it must be used fo just variables (not check indexes in array or strings)
**/

/**
* Return value of SCALAR variable if it defined without notices and error-handling.
* For safely check indexes (in string and arrays use {@see IS_SET_VAR})
*
* In most cases check like "if ($variable) $str = $variable . 'some'" is laconic form of more strict like "if (isset($variable) and $variable) $str = $variable . 'some'".
* So, if $variable was not defined yet we got notice. Well, when we do not need it, we can suppress it like "if (@$variable)"
* all seems good on first glance but we only supress error message, NOT error handling if it occures!
* So, if error handler was be set before (like set_error_handler("func_error_handler");) this error handler got control and stack will be broken!
*
* With that function we may safely use simple: $str = ISSET_VAR($variable) . 'some'...
*
* For Chec
*
* @param &mixed	$var variable amount of arguments.
* @return &mixed
**/
function &ISSET_VAR(&$var){
	if (isset($var)) return $var;
	else{
	$t = null; //To do not fire error "Only variables can be passed by reference in ..."
	return $t;
	}
}

function &IS_SET_VAR($what, &$where){
//MUST be explicit. It used in autoload.php, so, autoloading is not present yet!
	if (is_set($what, $where)) return $where[$what];
	else{
	$t = null; //To do not fire error "Only variables can be passed by reference in ..."
	return $t;
	}
}
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
*
* @package Debug
* @subpackage Debug
* @version 2.3.5
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-29 15:58 Version 2.3 from 2.2.b
*	- Add config-parameter "display_errors", default true.
*	- Move methods transformCorrect_print_r and transformCorrect_var_dump to separate class dump_utils (dump_utils.php)
*	- Move dump::log into log_dump.php in separate function log_dump.
*		dump::log ReRealise with it.
* 	It is isfull fo not only debug purpose, and very bad what it depends from much debug-tools (classes, functions, files)
*
*	* 2008-06-06 16:40 Ver 2.3 to 2.3.1
*	- Include Debug/log_dump.php (in dump::log) and realize dump::log through log_dump free function.
*	- Delete all deprecated free functions!
*
*	* 2008-08-27 19:15 Ver 2.3.1 to 2.3.2
*	- Handle xdebug.overload_var_dump option in dump::w
*
*	* 2008-09-15 22:15 Ver 2.3.2 to 2.3.3
*	- Prevent html-output in dump::c even if html_errors=On
*
*	* 2008-10-04 22:25 ver 2.3.3 to 2.3.4
*	- Add bacward-capability function implementation of function spl_object_hash() if it is not exists.
*
*	* 2009-01-30 15:10 ver 2.3.4 to 2.3.5
*	- Add
*	- All checks to $__CONFIG values replaced by call call to macros {@see ISSET_VAR}.
*		Full explanation reason of it see in description of macros {@see ISSET_VAR}
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

	if (null !== ISSET_VAR($GLOBALS['__CONFIG']['debug']['errorReporting'])){
	error_reporting($GLOBALS['__CONFIG']['debug']['errorReporting']);
	}

	if (null !== ISSET_VAR($GLOBALS['__CONFIG']['debug']['display_errors'])){
	ini_set('display_errors', $GLOBALS['__CONFIG']['debug']['display_errors']);
	}

	if (ISSET_VAR($GLOBALS['__CONFIG']['debug']['parseCallParam'])){


	}

/**
* @package Debug
* Mainly for emulate namespace
* Most (all?) methods are static
**/
class dump extends dump_utils{
	/**
	* Return $header. If in $header present - return as is, else make guess as real be invoked.
	*
	* @param &mixed $header. Be careful! By default, in parent methods like dump::*() $header=false!
	*	If passed $header === null it allows distinguish what it is not passed by default or
	*	it is not needed!!
	* @return &mixed $var
	**/
	static public function getHeader(&$header, &$var){
		if ($header){
		return $header;
		}
		elseif(
			//Be careful! Null, NOT false by default in dump::*()! It allows distinguish what it is
			//not passed by default or it is not needed!!
			$header !== null
			and ISSET_VAR($GLOBALS['__CONFIG']['debug']['parseCallParam'])
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
	*
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
	*
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
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param string|array	Callback-function or array(object, 'method')
	* @return string|void	Depend of parameter $return
	**/
	static public function buff($var, $header = false, $debug_func = 'print_r'){
	/*
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
	*
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
	*
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
	*
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
	*
	* @return mixed	::c() or ::w() invoke whith same parameters.
	**/
	static public function a($var, $header = false, $return = false){
	return self::auto($var, $header, $return);
	}#m a

	/**
	* One name to invoke dependently by out type.
	*
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

/**
* dump::getHeader assumed on spl_object_hash() for objects, so, we must emulate it on old versions of PHP.
* Simple implementation got from http://xpoint.ru/forums/programming/PHP/thread/38733.xhtml
*
* @param Object $obj
* @return string - object hash.
**/
if (!function_exists("spl_object_hash")) {
	function spl_object_hash($obj){
	static $cur_id = 0;
		if (!is_object($obj))
		return null;

		!isset($obj->_obj_id_) and $obj->_obj_id_ = md5($cur_id++);

	return $obj->_obj_id_;
	}
}
?>