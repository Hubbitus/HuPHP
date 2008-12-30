<?
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

include_once('Exceptions/BaseException.php');
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
				include_once('Debug/Tokenizer.php');
				}
			}

			$this->tok_ = Tokenizer::create(
				$this->bt->current()
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
include_once('Debug/backtrace.php');
?>