<?
/**
* ProcessException
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/*-inc
require_once('Exceptions/BaseException.php');
*/
/**
* @uses BaseException
**/

class ProcessException extends BaseException{
public $state = null;

	public function __construct($message = null, $code = 0, $pr) {
	$this->state = $pr;

	// make sure everything is assigned properly
	parent::__construct($message, $code);
	}#c
};
?>