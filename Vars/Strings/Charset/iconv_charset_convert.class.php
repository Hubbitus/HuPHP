<?
/**
* Charset encoding suite
* Iconv implementation
*
* @package Vars
* @subpackage charset_convert
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-06 16:08 ver 1.0 to 1.1
*	* Rename file from iconv_charset_convert.php to iconv_charset_convert.class.php (it will be reflected in framework)
*	* As it now part off Vars package move it into Vars directory (as part of /Strings directory).
*	* Assign @package Vars and turn current charset_convert to @subpackage.
*	* Modify include_once('charset_convert.php'); to include_once('Vars/Strings/Charset/charset_convert.class.php');
**/

/*-inc
include_once('Vars/Strings/Charset/charset_convert.class.php');
*/
/**
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
* @uses charset_convert
* @uses charset_convert_exception
**/
include_once('macroses/REQUIRED_VAR.php');

class iconv_charset_convert extends charset_convert{
	/**
	* Constructor. All as parent
	**/
	public function __construct($text, $inEnc = null, $outEnc = 'UTF-8'){
	parent::__construct($text, $inEnc, $outEnc);
	}#m __construct()

	/**
	* @inheritdoc
	* @Throws(charset_convert_exception, VariableRequiredException)
	**/
	public function convert(){
	REQUIRED_VAR($this->_in, 'InEncoding');
	REQUIRED_VAR($this->_out, 'OutEncoding');
	/*
	* iconnv do not provide any chance to handle errors. Even if provided charset is not correct, it only produce PHP Notice and return ampty string.
	* So, as last chance - catch this warning and convert it into exceprion!
	*/
	// BackUP settings
	$oldErrorHandler = set_error_handler( array($this, 'error_handler') );
	$oldErrorReporting = error_reporting(E_ALL);
	$this->_resText = iconv($this->_in, $this->_out, $this->_text);

	// Restore settings
	error_reporting($oldErrorReporting);
		if ($oldErrorHandler) set_error_handler( $oldErrorHandler );
		elseif (is_null($oldErrorHandler)) restore_error_handler();

		//Processing
		if ($this->_charset_convert_Errors){
//		dump::a($this->_charset_convert_Errors);
		$ttt = $this->_charset_convert_Errors; //Local buffer
		$this->_charset_convert_Errors = null; //Clear BEFORE throw, because if it will be catched correctly - it is not be cleared as well!
		throw new charset_convert_exception( implode(';', $ttt) );
		}
	}#m convert

	/**
	* Static equivalent of {@see ::convert()} for satac, fast invoke
	*
	* @return string of result
	**/
	static public function conv($text, $inEnc = null, $outEnc = 'UTF-8'){
	//This is correct only if Late Static Binding present. So, it starts from PHP 5.3.0
	// If we want make this code work on earler releases - just copy this function compleatly in derivates.
	$conv = new self($text, $inEnc, $outEnc);
	return $conv->getResult();
	}#m conv

protected $_charset_convert_Errors = '';
	function error_handler($errno, $errstr, $errfile, $errline /*, $errcontext */ ){
		if (stristr($errstr, 'iconv')){#This hack only fo MSSQL errors
		$this->_charset_convert_Errors[] = $errstr;
		//Don't execute PHP internal error handler
		return true;
		}
		else return false;# Default error-handler
	}#m error_handler
}
?>