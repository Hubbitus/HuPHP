<?
/**
* Charset encoding suite
*
* @package charset_convert
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
**/

include_once('Exceptions/Strings/charset/charset_convert_exception.php');
include_once('macroses/REQUIRED_VAR.php');

abstract class charset_convert{
protected $_in = null;
protected $_out = null;
protected $_text = null;
protected $_resText = null;

	/**
	* Constructor.
	*
	* @param string $text
	* @param string $inEnc
	* @param string $outEnc='UTF-8'
	**/
	public function __construct($text, $inEnc = null, $outEnc = 'UTF-8'){
	$this->setInEnc($inEnc);
	$this->setOutEnc($outEnc);
	$this->setText(REQUIRED_VAR($text, 'TextToConvert'));
		if ($inEnc and $outEnc) $this->convert();
	}#m __construct

	/**
	* Main working horse. Must be reimplemented each time we should provide new layer of xonversion (mb, iconv, recode etc)
	*
	* @return &$this
	**/
	abstract public function convert(); //{}#m convert

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

	/**
	* Set new In encoding
	*
	* @param string $enc New encoding
	* @return &$this
	**/
	public function &setInEnc($enc){
	$this->_in = $enc;
	$this->_resText = null;
	return $this;
	}#m setInEnc

	/**
	* Get current In encoding
	*
	* @return string
	**/
	public function getInEnc(){
	return $this->_in;
	}#m getInEnc

	/**
	* Set new Out encoding
	*
	* @param string $enc New encoding
	* @return &$this
	**/
	public function &setOutEnc($enc){
	$this->_out = $enc;
	$this->_resText = null;
	return $this;
	}#m setOutEnc

	/**
	* Get current Out encoding
	*
	* @return string
	**/
	public function getOutEnc(){
	return $this->_out;
	}#m getOutEnc

	/**
	* Set text to convert encoding.
	*
	* @package string $newText
	* @return &$this
	**/
	public function &setText($newText){
	$this->_text = $newText;
	return $this;
	}#m setText

	/**
	* Get current text
	*
	* @return string
	**/
	public function getText(){
	return $this->_text;
	}#m getText

	/**
	* Return result of convertation. If it is empty, run {@see ::convert()}
	*
	* @return string
	**/
	public function getResult(){
		if (empty($this->_resText)) $this->convert();
	return $this->_resText;
	}#m getResult

	/**
	* Auto convertion into string; {@see ->getResult()}
	*
	* @return
	**/
	public function __toString(){
	return $this->getResult();
	}#m __toString
} #c charset_convert
?>