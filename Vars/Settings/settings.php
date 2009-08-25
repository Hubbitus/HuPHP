<?
/**
* Provide easy to use settigns-cllass for many purpose. Similar array
* of settings, but provide several addition methods, and magick methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see
* class HuFormat}).
*
* @package Vars
* @subpackage settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0.5
*
* @changelog
*	* 2008-05-30 23:19
*	- Move include macroses REQUIRED_VAR.php and REQUIRED_NOT_NULL.php after declaration class
*	 settings to break cycle of includes
*
*	* 2009-01-18 14:57 (No version bump)
*	- Reflect renaming Class.php to HuClass.php
*
*	* 2009-05-08 10:37 ver 1.0.4 to 1.0.5
*	- Add magick ::__set() method
*	- return &$this; in ::setSetting() and ::__set() methods now.
**/

include_once('macroses/EMPTY_STR.php');
include_once('Vars/HuClass.php');	#Static method ::create()

class settings extends HuClass{
protected $__SETS = array();#Сами настройки, массив

	/**
	* Constructor.
	*
	* @param array=null $array
	**/
	function __construct(array $array = null){
		if ($array) $this->mergeSettingsArray($array);
	}#__c

	/**
	* Set setting by its name.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	&$this
	**/
	public function &setSetting($name, $value){
	$this->__SETS[$name] = $value;
	return $this;
	}#m setSetting

	/**
	* Rewrite ALL settings. To change only needed - use {@see ::setSetting()} method
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	* @return	nothing
	**/
	public function setSettingsArray(array $setArr){
	$this->__SETS = REQUIRED_VAR($setArr);
	}#m setSettingsArray

	/**
	* Rewrite provided settings by its values. To change single setting you may use {@see ::setSetting()}
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	**/
	public function mergeSettingsArray(array $setArr){
	/**
	* @internal
	* We don't use array_merge there because want preserv keys, even numerical:
	* http://ru2.php.net/manual/en/function.array-merge.php#92602
	* We also can't use simple array concatenation because want overwrite old values by new one...
	* So, doing all manually!
	**/
		foreach (REQUIRED_VAR($setArr) as $key => $val){
		$this->__SETS[$key] = $val;
		}
	}#m mergeSettingsArray

	/**
	* Return requested property by name. For more usefull access see {@see ::__get()} method.
	*
	* @param	string	$name
	* @return	mixed
	**/
	public function &getProperty($name){
	return $this->__SETS[REQUIRED_NOT_NULL($name)];
	}#m getProperty

	/**
	* Usefull alias of {@see ::setSetting()} to provide easy access in style of $obj->PropertyName = 'Some new value';
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	&$this
	**/
	public function &__set($name, $value){
	$this->setSetting($name, $value);
	return $this;
	}#m __set

	/**
	* Usefull alias of {@see ::getProperty()} to provide easy access in style of $obj->PropertyName
	*
	* @param	string	$name
	* @return	mixed
	**/
	public function &__get($name){
	return $this->getProperty($name);
	}#m __get

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of requested property
	* @return	boolean
	**/
	public function __isset($name) {
	return isset($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}#m __isset

	/**
	* Rreturn string in what merged settings by provided format.
	*
	* Descriptiopn of elements $fields {@see ::formatField()} method
	*
	* @param	array	$fields
	* @return	string
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
	*
	* @param	array|string	$field
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
	*
	* @return &$this
	**/
	public function &clear(){
	$this->__SETS = array();
	return $this;
	}#m clear

	/**
	* Return amount of settings.
	*
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
/**
* Parent class for more usefull using in parents who want be "customizable"
**/
class get_settings{
/** WARNING! Must be inicialised in parents! **/
protected /* settings */ $_sets = null;

	/**
	* Overload to provide ref on settings object. So, settings will be changable,
	* but can't be replaced settings object!
	*
	* @param <type> $name
	* @return	mixed
	**/
	public function &__get ($name){
		if ('settings' == $name) return $this->_sets;
	}#m __get

	/**
	* Return settings object
	*
	* @return	&Object(settings)
	**/
	public function &sets(){
	return $this->_sets;
	}#m sets
}#c get_settings