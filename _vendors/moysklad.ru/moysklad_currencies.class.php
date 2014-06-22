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
* Moysklad element currencies.
**/
class moysklad_currencies extends moysklad_element_base{
	function getName(){
		return 'currencies';
	}#m getName
}#c moysklad_currencies

/**
* Moysklad element script.
**/
class moysklad_currency extends moysklad_element_base {
	function getName() {
		return 'currency';
	}#m getName
}#c moysklad_currency
?>