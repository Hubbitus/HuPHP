<?
/**
* O-Range-SMS-partnerAPI ralated exceptions.
*
* @package Exceptions
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
*
* @changelog
*	* 2009-03-09 20:58 ver 1.0
*	- Initial import into Hubbitus repository
**/

/*-inc
require_once('Exceptions/BaseException.php');
*/
/**
* @uses BaseException
**/

class MSG_partnerAPIException extends BaseException{}

class MSG_AuthErrorException extends MSG_partnerAPIException{}
class MSG_SendFailException extends MSG_partnerAPIException{}
class MSG_InParseErrorException extends MSG_partnerAPIException{}
?>