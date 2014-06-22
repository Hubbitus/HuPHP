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
* Moysklad element connectors.
**/
class moysklad_connectors extends moysklad_element_base{
	function getName(){
		return 'connectors';
	}#m getName
}#c moysklad_connectors

/**
* Moysklad element script.
**/
class moysklad_connector extends moysklad_element_base {
	function getName() {
		return 'connector';
	}#m getName
}#c moysklad_connector
?>