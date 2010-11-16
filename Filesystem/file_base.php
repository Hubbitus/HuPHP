<?
/**
* Base file operations.
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.1
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
*	- Add in setPath initial initialization of $this->filename in any case! In case
*		if path is relative it will expanded. If not - old
*		behaviour it is not initialised!
*
*	* 2009-03-23 16:44 ver 1.2.1 to 1.2.2
*	- Method ::loadContent() changed to @return	&$this;. Full PhpDoc written.
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
*
*	* 2010-11-16 17:42 ver 2.0b to 2.1
*	- Add unlink method.
*	- Explicit inlude 'macroses/REQUIRED_NOT_NULL.php'.
**/

/*-inc
include_once('Exceptions/filesystem.php');

include_once('System/OS.php');
*/
include_once('macroses/REQUIRED_VAR.php');
include_once('macroses/REQUIRED_NOT_NULL.php');
/**
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses FileNotExistsException
* @uses FileNotReadableException
* @uses OS
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
	* Unlink (delete) file
	*
	* @return>boolean
	**/
	public function unlink(){
	return unlink($this->path());
	}#m unlink

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
	* @Throws(VariableRequiredException)
	**/
	public function &setContentFromString($string){
	$this->content = REQUIRED_NOT_NULL($string);
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
		false !== ($count = @file_put_contents($this->path(), $this->content, $flags, $resource_context))
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
			throw new FileNotReadableException('Unknown error operate on file.', $this->path());
		}
	}#m checkOpenError
}#c file_base
?>