<?
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

class DBException extends BaseException{
	public $db;

	public function __construct($message, database &$db){
		parent::__construct($message);
		$this->db = $db;
	}#__c
}

class ConnectErrorDBException extends DBException{
	public $DBError;//Ref to database_error object
}
class DBSelectErrorDBException extends DBException{}
class QueryFailedDBException extends DBException{}

class DBnullGetException extends DBException{}//Got empty results from DB
?>