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
* Moysklad element processings.
**/
class moysklad_processings extends moysklad_element_base{
	function getName(){
		return 'processings';
	}#m getName
}#c moysklad_processings

/**
* Moysklad element script.
**/
class moysklad_processing extends moysklad_element_base {
	function getName() {
		return 'processing';
	}#m getName
}#c moysklad_processing
?>