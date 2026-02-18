<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Exceptions\Variables;

use Hubbitus\HuPHP\Debug\Tokenizer;
use Hubbitus\HuPHP\Debug\Backtrace;

/**
* @TODO Rewrite to use internal Exception backtrace
**/
class VariableRequiredException extends VariableException {
	public $bt = null;
	private $var = null;

	private $tok_ = null;

	public function __construct(?Backtrace $bt = null, $varname = null, ?string $message = null, int $code = 0) {
		$this->bt = $bt;
		$this->var= $varname;

		parent::__construct($message ?? '', $code);
	}
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
	* @return Tokenizer
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
}
