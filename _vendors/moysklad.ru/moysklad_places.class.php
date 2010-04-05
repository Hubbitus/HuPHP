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
*
* @changelog
*	* 2010-04-01 01:15 ver 1.0
*	- Initial version.
**/

/**
* Moysklad element places.
*
* @author Pavel Alexeev aka Pahan-Hubbitus
* @created 2010-04-01 01:15 ver 1.0
* @copyright 2010 Pavael Alexeev Aka Pahan-Hubbitus
**/
class moysklad_places extends moysklad_element_base{
	function getName(){
	return 'places';
	}#m getName
}#c moysklad_places

/**
 * Moysklad element script.
 *
 * @author Pavel Alexeev aka Pahan-Hubbitus
 * @created 2010-04-01 16:47 ver 1.0
 * @copyright 2010 Pavael Alexeev aka Pahan-Hubbitus
 **/
class moysklad_place extends moysklad_element_base {
	function getName() {
		return 'place';
	}#m getName
}#c moysklad_place