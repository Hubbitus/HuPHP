<?
/**
* ClassExceptions
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1
* @created ?2008-05-31 5:26 ver 1.0 to 1.1
*
* @uses BaseException
**/

class ClassException extends BaseException{}

class ClassUnknownException extends ClassException{}
class ClassNotExistsException extends ClassException{}
class ClassMethodExistsException extends ClassException{}
class ClassPropertyNotExistsException extends ClassException{}
?>