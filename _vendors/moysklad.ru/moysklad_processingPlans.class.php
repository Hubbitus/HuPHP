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
* Moysklad element processingPlans.
**/
class moysklad_processingPlans extends moysklad_element_base{
	function getName(){
	return 'processingPlans';
	}#m getName
}#c moysklad_processingPlans

/**
* Moysklad element script.
**/
class moysklad_processingPlan extends moysklad_element_base {
	function getName() {
		return 'processingPlan';
	}#m getName
}#c moysklad_processingPlan
?>