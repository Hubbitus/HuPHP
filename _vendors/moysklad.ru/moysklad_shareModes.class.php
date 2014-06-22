<?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Moysklad element shareModes.
**/
class moysklad_shareModes extends moysklad_element_base{
	function getName(){
		return 'shareModes';
	}#m getName
}#c moysklad_shareModes

/**
* Moysklad element shareMode.
**/
class moysklad_shareMode extends moysklad_element_base{
	function getName(){
		return 'shareMode';
	}#m getName
}#c moysklad_shareMode
?>