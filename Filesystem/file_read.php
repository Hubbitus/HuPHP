<?
/**
* Operations with file by serial read/write
*
* @package Filesystem
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.0b
*
* @changelog
*	* 2009-03-25 13:51 ver 2.0b
*	- Initial SPLITTED version. See changelog of file_base.php
*	- Add method ::lineNo()
**/

/*-inc
include_once('file_base.php');
include_once('Vars/VariableStream.php');
*/
include_once('macroses/REQUIRED_VAR.php');
/**
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses file_base
* @uses VariableStream
**/

class file_read extends file_base{
private $fd = null;

protected $_line_no = 0; //Current line number. Read only. For getline() access.

	/**
	* If file opened befor, content will be written in current position of file.
	* If it wasn't opened - open occured.
	* @inheritdoc
	*
	* @param	integer	Append by default if descriptor opened.	FILE_USE_INCLUDE_PATH supported if fd not opened en we open new.
	* @param	resource	$resource_context See {@link http://php.net/stream-context-create}.
	*	Used only if file opened here (was NOT opened before)
	* @return	integer	Count of written bytes
	**/
	public function writeContent($flags = null, $resource_context = null){
		if (!$this->fd) $this->open('w', ( $flags & FILE_USE_INCLUDE_PATH ), $resource_context);
	/*
	* To provide consistence API and do not fake incoming method parameters we must use streams.
	* There present function stream_get_contents, but I not found opposite, which can write string to stream.
	* My decision to use stream_copy_to_stream() function, but for that I must have another stream to copy from.
	* I not found standard way to map variable on stream, so, use VariableStream in conditional of global temp variable
	*
	* Another possible way, may be using 'php://memory' or 'php://temp' (http://mikenaberezny.com/2008/10/17/php-temporary-streams/),
	* but in this case full variable data must be explicid copyed in this stream.
	* VariableStream with variable reference give mor migick on my opinion.
	*/
	$GLOBALS['__tmp_content_var_stream'] =& $this->content;
	$this->checkOpenError(
		#$this->rawFilename because may be file generally not exists!
		(bool)( $count = @stream_copy_to_stream($this->fd, ($tfd = fopen('var://__tmp_content_var_stream'))) )
	);
	$this->_writePending = false;
	fclose($tfd);
	return $count;
	}#m writeContent

###########################
### Self introduced methods
###########################

	/**
	* Open file for reading/writing (according to $mode)
	*
	* @param	string	$mode. See {@link http://php.net/fopen}
	* @param	boolean	$use_include_path
	* @param	resource	$zcontext  See {@link http://php.net/fopen}
	**/
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

	/**
	* Get next line from stream.
	*
	* @param	integer $length. Optional - maximum length of string. If null - all string returned (by default).
	* @return	string
	* @Throws(VariableRequiredException)
	**/
	public function getline($length = null){
	++$this->_line_no;
	return $length ? fgets(REQUIRED_VAR($this->fd), $length) : fgets(REQUIRED_VAR($this->fd));
	}#m getline

	/**
	* Return current line number in getline() mode access.
	*
	* WARNING! Please keep in mind, it is not provide reliable interface to calculate real lines.
	* In current implementation by the fact it reflect count of invokes method ::getline() only!!!
	*
	* @return	integer
	**/
	function lineNo(){
	return $this->_line_no;;
	}#m lineNo

	/**
	* Return tail of stream as string.
	*
	* {@link http://php.net/stream-get-contents}
	*
	* @param	integer	$maxlength
	* @param	offset	$offset
	* @return	string
	**/
	public function getTail ($maxlength = -1, $offset = 0){
	return stream_get_contents($this->fd, $maxlength, $offset);
	}#m getTail
}#c file_read
?>