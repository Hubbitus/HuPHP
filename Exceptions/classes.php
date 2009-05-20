<?
/**
* ClassExceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
*
* @changelog
*	* 2008-05-31 5:26 v 1.0 to 1.1
*	- Add ClassUnknownException
**/

/*-inc
require_once('Exceptions/BaseException.php');
*/
/**
* @uses BaseException
**/

class ClassException extends BaseException{}

class ClassUnknownException extends ClassException{}
class ClassNotExistsException extends ClassException{}
class ClassMethodExistsException extends ClassException{}
class ClassPropertyNotExistsException extends ClassException{}
?>