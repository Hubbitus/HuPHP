<?
/**
* BaseException
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

class BaseException extends Exception{
	#$pos = false - at end, else - in begining
	public function ADDMessage($addmess, $pos = false){
		if (!$pos) $this->message .= $addmess;
		else $this->message = $addmess.$this->message;
	}
}
?>