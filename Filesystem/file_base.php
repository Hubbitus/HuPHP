<?
/**
* Base file operations.
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.2.1
*
* @changelog
*	* 2008-08-27 ver 1.0 to 1.1
*	- Added methods: clearPendingWrite(), __destructor(), appendString()
*
*	* 2009-01-25 00:00 ver 1.1 to 1.2
*	- Modify setPath() to set full path into ->filename. ->rawFilename filled also.
*	- Add method: rawPath().
*	- Add include_once('System/OS.php'); (for the OS::isPathAbsolute)
*
*	* 2009-02-26 15:59 ver 1.2 to 1.2.1
*	- Add in setPath initial initialization of $this->filename in any case! In case if path is relative it will expanded. If not - old
*	  behaviour it is not initialised!
**/

include_once('macroses/REQUIRED_VAR.php');
include_once('Exceptions/filesystem.php');

include_once('System/OS.php');

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
	if ($filename) $this->setPath($filename);
}#__c

#Write all pendings write
public function __destruct(){
	if ($this->_writePending) $this->writeContents();
}#__d

/**
* Set new path. For example to writing new file.
*
* @param $filename	string New filename
* @return &$this
**/
public function &setPath($filename){
$this->filename = $this->rawFilename = $filename;
/**
* And we MUST set full path in ->filename because after f.e. chdir(...) relative path may change sense.
* Additionally, in __destruct call to getcwd return '/'!!! {@See http://bugs.php.net/bug.php?id=30210} 
**/
	if (!($realpath = realpath($this->rawFilename))){//We can't direct use $this->filename instead of $realpath because if it ! we not always want null it!
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
**/
public function path(){
return $this->filename;
}

/**
* Return curent RAW (what wich be passed into the {@see setPath()}, without any transformation) path.
**/
public function rawPath(){
return $this->rawFilename;
}

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

public function getDir(){return dirname($this->path());}

/**
* Return array of lines
*
* @param array $lines. If empty array - whole array of lines. Else Array(int $offset  [, int $length  [, bool $preserve_keys  ]] ). See http://php.net/array_slice
* @param boolean(true) $updateLineSep. See explanation in ->explodeLines() method.
* @return array Array of lines
* @Throw(VariableEmptyException)
**/
public function getLines(array $lines = array(), $updateLineSep = true){
$this->checkLoad();
	if (!$this->lineContent) $this->explodeLines($updateLineSep);

	if(!empty($lines)) return call_user_func_array('array_slice', array_merge(array( 0 => $this->lineContent), $lines) );
	else return $this->lineContent;
}#m getLines

/**
* Explode whole contents by lines.
*
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
*
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
*
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
* Set content to write.
*
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
*
* @param string	$string. String to append from.
* @return &$this
* @Throw(VariableRequiredException)
**/
public function &appendString($string){
return $this->setContentFromString($this->content . REQUIRED_VAR($string));
}#m appendString

/**
* Writes whole contents to file (filename may be set via ->setPath('NewFileName'))
*
* @param	integer	flags See http://php.net/file_put_contents
* @param	resource	$resource_context See http://php.net/file_put_contents
* @param	string	$implodeWith See descr ->implodeLines()
* @param	boolean	$updateLineSep See descr ->implodeLines()
* @return	integer	Count of written bytes
**/
public function writeContents($flags = null, $resource_context = null, $implodeWith = null, $updateLineSep = true){
$this->checkOpenError(
	#$this->rawFilename because may be file generally not exists!
	(bool) ($count = @file_put_contents($this->path(), $this->getBLOB($implodeWith, $updateLineSep), $flags, $resource_context))
);
$this->_writePending = false;
return $count;
}#m writeContent

/**
* Calculate lie number by offset. Line-separators on end of string.
*
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
* Opposit to {@see ::getLineByOffset()}
*
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
?>