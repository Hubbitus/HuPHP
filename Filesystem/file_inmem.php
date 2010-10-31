<?
/**
* Operations with file in memory.
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0.1b
*
* @changelog
*	* 2009-03-25 13:51 ver 2.0b
*	- Initial SPLITTED version. See changelog of file_base.php
*	- Fix few minor bugs (in ::implodeLines(), ::getBLOB())...
*	- Change methods ::setLineSep() and ::checkLoad() to return &$this
*
*	* 2009-03-31 18:14 ver 2.0b to 2.0.1b
*	- Explicit check "false !==" instead of just casting to bool. So, read zero-length file is also positive result.
*
*	* 2009-12-14 11:47 ver 2.0.1b to 2.0.2
*	- Add methods ::iconv(), ::enconv()
**/

/*
include_once('file_base.php');
*/
/**
* @uses REQUIRED_VAR
* @uses VariableRequiredException
* @uses file_base
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
	* @return	&$this
	**/
	public function &loadContent($use_include_path = false, $resource_context = null, $offset = null, $maxlen = null){
	$this->checkOpenError(
		false !==
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
	* @Throws(VariableRequiredException)
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
		false !==  ($count = @file_put_contents($this->path(), $this->getBLOB($implodeWith, $updateLineSep), $flags, $resource_context))
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

	/**
	* Iconv content from one charset to enother. If in charset is not known consider use method {@see ::enconv()}
	*
	* @uses iconv
	* @param	string	$fromEnc
	* @param	string=UTF-8	$toEnc
	* @return	&$this
	**/
	public function &iconv($fromEnc, $toEnc = 'UTF-8'){
	$this->setContentFromString(iconv($fromEnc, $toEnc, $this->getBLOB()));
	return $this;
	}#m iconv

	/**
	* Uses shell execute enconv to guess encoding and convert it to desired
	*
	* @uses Process
	* @uses shell enconv
	* @param	string=russian	$lang
	* @param	string=UTF-8	$toEnc
	* @return	&$this;
	**/
	public function &enconv($lang = 'russian', $toEnc = 'UTF-8'){
	$this->setContentFromString(Process::exec("enconv -L $lang -x $toEnc", null, null, $this->getBLOB()));
	return $this;
	}#m enconv
}#c file_inmem
?>