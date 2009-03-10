/**
* Database exceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
*
* @changelog
*	* 2009-03-10 07:55 ver 1.0 to 1.1
*	- Add public $DBError property into ConnectErrorDBException exception
**/
<?
include_once('Exceptions/BaseException.php');

class DBException extends BaseException{}

class ConnectErrorDBException extends DBException{
public $DBError;//Ref to database_error object
}
class DBSelectErrorDBException extends DBException{}
class QueryFailedDBException extends DBException{}

class DBnullGetException extends DBException{}//Got empty results from DB
?>