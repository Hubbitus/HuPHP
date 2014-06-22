<?
/**
* VariablesExceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 2.1
* @created ?2008-05-29 17:51 ver 2.0b to 2.1
*
* @uses BaseException
* @uses backtrace
**/

class VariableException extends BaseException{};

/**
* @TODO Rewrite to use internal Exception backtrace
**/
class VariableRequiredException extends VariableException{
	public $bt = null;
	private $var = null;

	private $tok_ = null;

	public function __construct(backtrace &$bt, $varname = null, $message = null, $code = 0) {
		$this->bt = $bt;
		$this->var= $varname;

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
?>