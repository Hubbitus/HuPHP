<?
/**
* Provide easy to use settigns-cllass for many purpose. Similar array
* of settings, but provide several addition methods, and magick methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see
* class HuFormat}).
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-30 23:19
*	- Move include macroses REQUIRED_VAR.php and REQUIRED_NOT_NULL.php after declaration class
*	 settings to break cycle of includes
*
*	* 2009-01-18 14:57 (No version bump)
*	- Reflect renaming Class.php to HuClass.php
**/

include_once('macroses/EMPTY_STR.php');
include_once('Vars/HuClass.php');	#Static method ::create()

class settings extends HuClass{
protected $__SETS = array();#Сами настройки, массив

	/**
	* Constructor.
	* @param array=null $array
	**/
	function __construct(array $array = null){
		if ($array) $this->mergeSettingsArray($array);
	}#constructor

	public function setSetting($name, $value){
	$this->__SETS[$name] = $value;
	}

	#ПЕРЕЗАПИСЫВАЕТ ВСЕ настройки. Для изменения отдельных - setSetting
	#Хорошо было бы это все в setSettings запихать, но перегрузка не поддерживается :(. Что ж, будут разные именаю
	public function setSettingsArray(array $setArr){
	$this->__SETS = REQUIRED_VAR($setArr);
	}

	#ПЕРЕЗАПИСЫВАЕТ УКАЗАННЫЕ настройки. Для изменения отдельных - setSetting
	#Хорошо было бы это все в setSettings запихать, но перегрузка не поддерживается :(. Что ж, будут разные именаю
	public function mergeSettingsArray(array $setArr){
	$this->__SETS = array_merge((array)$this->__SETS, REQUIRED_VAR($setArr));
	}

	public function getProperty($name){
	return ($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}

	function __get($name){
	return $this->getProperty($name);
	}

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	* @param	string	$name	Name of required property
	* @return boolean
	*/
	public function __isset($name) {
	return isset($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}#m __isset

	/**
	* Возвращает строку, в которую объединены требуемые (по представленному порядку) настройки.
	* Descriptiopn of elements $fields {@see ::formatField}
	**/
	public function getString(array $fields){
	$str = '';
		foreach (REQUIRED_VAR($fields) as $field){
		$str .= $this->formatField($field);
		}
	return $str;
	}#m getString

	/**
	* Format Field primarly for {@see ::getString}, but may be used and separatly
	* $field one of:
	*	1) Именем настройки. Если найдена такая настройка и она не пуста, подставляется она
	*	2) Просто константной строкой, тогда выводится как есть
	*	2) Массивом, формата:
	*		array(
	*		'str' => Имя настройки. (обязательно)
	*		'prefix' => ''
	*		'suffix' => ''
	*		'defValue' => ''
	*		)
	*		Вместо ассоциативного массива, допустимы и числовые стандартные индексы, чтобы короче писать не:
	*		array('str' =>'tag', 'prefix' => '<', 'suffix' => '>', 'defValue' => '<unknown>'),
	*		а просто, коротко и красиво
	*		array('tag', '<', '>', '<unknown>'),
	*		Передаются в макрос NON_EMPTY_STR, см. его для подробностей
	* @return string
	**/
	public function formatField($field){
		if (is_array($field)){
			if (!isset($field[0])) $field = array_values($field);
		return NON_EMPTY_STR(@$this->getProperty($field[0]), @$field[1], @$field[2], @$field[3]);
		}
		else{
		return EMPTY_STR(@$this->getProperty($field), $field);#Или по имени настройку, если это просто текст;
		}
	}#m formatField

	/**
	* Clear all settings
	* @return $this
	*/
	public function clear(){
	$this->__SETS = array();
	}#m clear

	/**
	* Number of settings.
	* @return integer
	**/
	public function length(){
	return sizeof($this->__SETS);
	}#m length
	
}#c settings

/**
* It's Before declaration of VariableRequiredException may produce cycle of includes...
**/
include_once('macroses/REQUIRED_VAR.php');
include_once('macroses/REQUIRED_NOT_NULL.php');

#Для удобного наследования
class get_settings{
//НЕ забыть его где-то инициализировать!!!
protected /* settings */ $_sets = null;

	public function &__get ($name){#Переопределяем, чтобы сделать ссылку на настройки не изменяемой!
	#таким образом настройки менять можно будет, а сменить объект настроек - нет
		if ('settings' == $name) return $this->_sets;
	}#__get

	/**
	* Return settings
	* @return	&Object(settings)
	*/
	public function &sets(){
	return $this->_sets;
	}#m sets
}#c get_settings