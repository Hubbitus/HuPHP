<?
include_once('Exceptions/BaseException.php');

class DBException extends BaseException{}

class ConnectErrorDBException extends DBException{}
class DBSelectErrorDBException extends DBException{}
class QueryFailedDBException extends DBException{}

class DBnullGetException extends DBException{}//Got empty results from DB
?>