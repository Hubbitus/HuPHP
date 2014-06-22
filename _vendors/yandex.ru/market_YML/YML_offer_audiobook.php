<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

class YML_offer_attributes_audiobook extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'audiobook'
		,'available'	=> true
	);
}#c YML_offer_attributes_audiobook

class YML_offer_audiobook extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		//	author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, table_of_contents?, performed_by?, perfomace_type?, storage?, format?, recording_lenght?
		'author'		//? Автор произведения
		,'name'		// Наименование произведения
		,'publisher'	//? Издательство
		,'series'		//? Серия
		,'year'		//? Год издания
		,'ISBN'		//? Код книги, если их несколько, то указываются через запятую.
		// Present in description, but not allowed by DTD
		//	,'description'	// Аннотация к книге.
		,'volume'		//? Количество томов.
		,'part'		//? Номер тома.
		,'language'	//? Язык произведения.
		,'table_of_contents'	//? Оглавление. Выводится информация о наименованиях произведений, если это сборник рассказов или стихов.

		,'performed_by'	//? Исполнитель. Если их несколько, перечисляются через запятую
		,'performance_type'	//? Тип адиокниги (радиоспектакль, произведение начитано, ...)
		,'storage'		//? Носитель, на котором поставляется аудиокнига.
		,'format'			//? Формат аудиокниги.
		/**
		* WARNING! recording_lenght format is completely absent in DTD (mentioned
		*	as PCDATA) and have another format in example then in description!
		*	Current implementation by description
		* WARNING2! It is recording_lenght, NOT recording_length in DTD!
		**/
		,'recording_lenght'	//? Время звучания задается в формате mm.ss (минуты.секунды).
);

	public function __construct(array $array, YML_offer_attributes_audiobook $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		$this->addFilterSet(new settings_filter_base('recording_length', array($this, 'filter_set__check_recording_length')));

		parent::__construct($array, $props, $currencies, $categories);
	}#__c

	/**
	* Filter: Check on set what number of pages integer positive number.
	*
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_recording_length($name, &$val){
		if (!preg_match('/^\d+\.\d{2}$/')) throw new YML_offer_exception_constraint('Recording time must be a string in format: "mm.ss (minutes.seconds)"');
		return $val;
	}#m filter_check_recording_length
}#c YML_offer_audiobook
?>