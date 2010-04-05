<?
/**
* moysklad.ru XML class usage example.
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
*
* @changelog
*	* 2010-03-30 15:15 ver 1.0
*	- Initial version.
**/

//EXAMPLE:
include_once('autoload.php');
include_once('/home/_SHARED_/_vendors/moysklad.ru/moysklad.class.php');

$xml = new moysklad();

$company		= 'hubbitus';
$updatedBy	= 'admin@hubbitus';

$xml->add_goodFolder(
	new moysklad_goodFolder(
		array(
			'name'		=> 'Тестовая категория'
			,'updatedBy'	=> $updatedBy
			// Elements
			,'id'		=> 0
			,'company'	=> $company
			,'description'	=> 'Тестовые данные импорта'
		)
	)
);
/*
$xml->add_uom(
	new moysklad_uom(
		array(
			'name'		=> 'шт'
			,'updatedBy'	=> $updatedBy
			// Attributes
			,'id'		=> 0
			,'version'	=> 0
			,'company'	=> $company
			,'description'	=> 'Тестовые данные импорта'
		)
	)
);
*/
$xml->add_good(
	new moysklad_good(
		array(
			'minimumBalance'	=> 0		#"20.0"
			,'buyPrice'		=> 10
			,'salePrice'		=> 20
			,'uomId'			=> 'tVD4k03EhnKBPbeXSY9RW2' /*0*/
			,'parentId'		=> 0	# CategoryId!
			,'name'			=> 'Наш классный товар'
			,'updatedBy'		=> $updatedBy
			// elements
			,'id'			=> 0
			,'version'		=> 0
			,'company'		=> $company
			,'description'		=> 'Тестовые данные импорта'
		)
	)
);

echo trim($xml->saveXML());
?>