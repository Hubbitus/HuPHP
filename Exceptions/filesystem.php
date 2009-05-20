<?
/**
* FileSystem Exceptions
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/*-inc
require_once('Exceptions/BaseException.php');
*/
/**
* @uses BaseException
**/

class FilesystemException extends BaseException{
protected $fullPath = '';

	function __construct($message, $fullPath){
	$this->fullPath = $fullPath;
	parent::__construct($message);
	}

	// custom string representation of object
	public function __toString(){
	return __CLASS__ . ": [{$this->fullPath}]: {$this->message}\n";
	}
}

class RemoteGetException extends FilesystemException{}

class FileLoadErrorException extends FilesystemException{}
class FileNotReadableException extends FileLoadErrorException{}
class FileNotExistsException extends FileLoadErrorException{}

?>