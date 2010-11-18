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

/*-inc
require_once('Exceptions/BaseException.php');
*/
/**
* @uses BaseException
* @uses backtrace
**/

class VariableException extends BaseException{};

/**
* @TODO Rewrite to use internal Exception backtrace
* @author pasha
**/
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
			/*-inc
			if (!class_exists('Tokenizer')){
				if(@$__CONFIG['debug']['parseCallParam'] or !@NO_DEBUG){
				include_once('Debug/Tokenizer.php');
				}
			}
			*/

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

class VariableReadOnlyException	extends VariableException{}

/**
* It's Before declaration of VariableRequiredException may produce cycle of includes...
**/
/*-inc
include_once('Debug/backtrace.php');
*/
?>