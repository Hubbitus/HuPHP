<?
/**
* O-Range-SMS-partnerAPI ralated exceptions.
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created ?2009-03-09 20:58
*
* @uses BaseException
**/

class MSG_partnerAPIException extends BaseException{}

class MSG_AuthErrorException extends MSG_partnerAPIException{}
class MSG_SendFailException extends MSG_partnerAPIException{}
class MSG_InParseErrorException extends MSG_partnerAPIException{}
?>