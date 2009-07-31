<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
*
* @changelog
*	* 2009-06-30 17:21 ver 1.0
*	- Initial version.
**/

//EXAMPLE:
include_once('autoload.php');

$yml = new YML;
/*
$yml->addShop(
	new HuArray(
		array(
			'name'		=> 'Stroydostavka.com - интернет-магазин строительных товаров'
			,'company'	=> 'ООО "Корда"'
			,'url'		=> 'http://stroydostavka.com'
		)
	)
)->addCurrencies(
	array(
		'RUR'	=> array(
			'rate' => 1
		)
		,'USD'	=> array(
			'rate' => '29.30'
		)
		,'EUR'	=> array(
			'rate' => 'CBRF'
			,'plus'=> 3
		)
	)
)->addCategories(
	array(
		// id="1". If no 'parentId' - root category
		1 => array(
			'value'	=> 'Книги'
		)
		, //id="2"
		2 => array(
			'value'	=> 'Видео'
		)
		,
		3 => array(
			'value'	=> 'Детективы'
			,'parentId'	=> '1'
		)
		,
		4 => array(
			'value'	=> 'Боевики'
			,'parentId'	=> '1'
		)
	)
)->addOffer(
	new YML_offer_vendormodel(
		array(
			'url'	=> 'http://stroydostavka.com/product.php?productid=16227&cat=121&page=1'
			,'price'	=> 123.456
			,'currencyId'	=> 'RUR'
			,'categoryId'	=> 1
			,'picture'	=> 'http://stroydostavka.com/images/P/FK01003041.jpg'
			,'name'		=> 'Радиатор стальной "KERMI"'
			,'vendorCode'	=> 'FKV220504'
			,'description'	=> 'ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ:
# Высота: 500 мм
# Длина: 400 мм
# Мощность: 772 Вт
# Рабочее давление: 8,7 бар
# Материал: сталь
# Цвет: белый'
		)
		,new YML_offer_attributes_vendormodel(
			array(
				'id' => 123
				,'bid'	=> 0.25
				,'cbid'	=> 0.30
			)
		)
		,$yml->getCurrencies()
		,$yml->getCategories()
	)
);
*/

$yml->addShop(
	array(
                'name'		=> 'Stroydostavka.com - интернет-магазин строительных товаров'
                ,'company'	=> 'ООО "Корда"'
                ,'url'		=> 'http://stroydostavka.com'
	)
);
$yml->addCurrencies(
	array(
		'RUR'	=> array(
			'rate' => 1
		)
		,'USD'	=> array(
			'rate' => '29.30'
		)
		,'EUR'	=> array(
			'rate' => 'CBRF'
			,'plus'=> 3
		)
	)
);
$yml->addCategories(
	array(
		// id="1". If no 'parentId' - root category
		1 => array(
			'value'	=> 'Книги'
		)
		, //id="2"
		2 => array(
			'value'	=> 'Видео'
		)
		,
		3 => array(
			'value'	=> 'Детективы'
			,'parentId'	=> '1'
		)
		,
		4 => array(
			'value'	=> 'Боевики'
			,'parentId'	=> '1'
		)
	)
);
$yml->addOffer(
	new YML_offer_vendormodel(
		array(
			'url'	=> 'http://stroydostavka.com/product.php?productid=16227&cat=121&page=1'
			,'price'		=> 123.456
			,'currencyId'	=> 'RUR'
			,'categoryId'	=> 1
//			,'typePrefix'	=> 'Радиатор'
			,'typePrefix'	=> 'Радиатор стальной "KERMI"'
			,'picture'		=> 'http://stroydostavka.com/images/P/FK01003041.jpg'
//			,'name'			=> 'Радиатор стальной "KERMI"'
			,'vendor'		=> 'Kermi'
//			,'vendorCode'	=> 'FKV220504'
			,'model'		=> 'FKV220504'
			,'description'	=> 'ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ:
# Высота: 500 мм
# Длина: 400 мм
# Мощность: 772 Вт
# Рабочее давление: 8,7 бар
# Материал: сталь
# Цвет: белый'
		)
		,new YML_offer_attributes_vendormodel(
			array(
				'id' => 123
				,'bid'	=> 0.25
				,'cbid'	=> 0.30
			)
		)
		,$yml->getCurrencies()
		,$yml->getCategories()
	)
);
echo trim($yml->saveXML());
?>