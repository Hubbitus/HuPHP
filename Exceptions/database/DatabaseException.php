<?php
declare(strict_types=1);

/**
* Database exceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
* @created ?2009-03-10 07:55 ver 1.0 to 1.1
*
* @uses BaseException
**/

class DatabaseException extends BaseException{
	public $db;

	public function __construct($message, Database &$db){
		parent::__construct($message);
		$this->db = $db;
	}
}
