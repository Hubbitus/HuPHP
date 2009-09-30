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

class YML_offer_attributes_eventticket extends YML_offer_attributes{
//Defaults
protected $__SETS = array(
	'type' => 'artist.title'
	,'available'	=> true
);
}#c YML_offer_attributes_eventticket

/**
* YML event ticket offer.
**/
class YML_offer_eventticket extends YML_offer{
// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
public $properties_addon = array(
// name, place, hall?, hall_part?, date, is_premiere?, is_kids?
	'name'		# Название мероприятия
	,'place'		# Зал

//?? Написал вопрос. Полагаю что все же верно hall_part, как в DTD, а не hall plan (да еще и с пробелом), как в описании
//	,'hall plan'	# Ссылка на картинку версии зала
	,'hall_part'	#?
	,'date'		# Дата и время сеанса. Указываются в формате ISO 8601: YYYY-MM-DDThh:mm
	,'is_premiere'	#? Признак премьерности мероприятия
	,'is_kids'	#? Признак детского мероприятия
);

	public function __construct(array $array, YML_offer_attributes_eventticket $props, DOMNode $currencies, DOMNode $categories = null){
	$this->nesting();

	parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_eventticket
?>