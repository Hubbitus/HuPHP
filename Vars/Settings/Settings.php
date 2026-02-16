<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\HuClass;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;
use function Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL;
use function Hubbitus\HuPHP\Macroses\EMPTY_STR;
use function Hubbitus\HuPHP\Macroses\NON_EMPTY_STR;

/**
* Provide easy to use settings-class for many purpose. Similar array
* of settings, but provide several addition methods, and magic methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see HuFormat} class).
*
* @package Vars
* @subpackage settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0.5
* @created 2008-05-30 23:19
**/
class Settings extends HuClass {
	protected $__SETS = []; // Array of settings itself

	/**
	* Constructor.
	*
	* @param array $array
	**/
	public function __construct(array $array = []){
		if ($array) $this->mergeSettingsArray($array);
	}

	/**
	* Set setting by its name.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	&$this
	**/
	public function &setSetting($name, $value): static {
		$this->__SETS[$name] = $value;
		return $this;
	}

	/**
	* Rewrite ALL settings. To change only needed - use {@see ::setSetting()} method
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	* @return	void
	**/
	public function setSettingsArray(array $setArr): void {
		$this->__SETS = REQUIRED_VAR($setArr);
	}

	/**
	* Rewrite provided settings by its values. To change single setting you may use {@see ::setSetting()}
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param	array	$setArr
	**/
	public function mergeSettingsArray(array $setArr): void {
		/**
		* @internal
		* We don't use array_merge there because want preserve keys, even numerical:
		* http://ru2.php.net/manual/en/function.array-merge.php#92602
		* We also can't use simple array concatenation because want overwrite old values by new one...
		* So, doing all manually!
		**/
		foreach (REQUIRED_VAR($setArr) as $key => $val){
			$this->__SETS[$key] = $val;
		}
	}

	/**
	* Return requested property by name. For more useful access see {@see ::__get()} method.
	*
	* @param        string  $name
	* @return       mixed
	**/
	public function &getProperty($name){
			return $this->__SETS[REQUIRED_NOT_NULL($name)];
	}

	/**
	* useful alias of {@see ::setSetting()} to provide easy access in style of $obj->PropertyName = 'Some new value';
	*
	* @param        string  $name
	* @param        mixed   $value
	* @return       &$this
	**/
	public function &__set($name, $value) {
			$this->setSetting($name, $value);
			return $this;
	}
	/**
	* useful alias of {@see ::getProperty()} to provide easy access in style of $obj->PropertyName
	*
	* @param        string  $name
	* @return       mixed
	**/
	public function &__get($name){
			return $this->getProperty($name);
	}

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param        string  $name   Name of requested property
	* @return       boolean
	**/
	public function __isset($name): bool {
			return isset($this->__SETS[REQUIRED_NOT_NULL($name)]);
	}

	/**
	* Get string representation of settings.
	* @see ::formatField()
	* @param	array	$fields
	* @return	string
	**/
	public function getString(array $fields): string {
		$str = '';
		foreach (REQUIRED_VAR($fields) as $field){
			$str .= $this->formatField($field);
		}
		return $str;
	}
	/**
	* Format Field Primarily for {@see ::getString}, but may be used and separately
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
	public function formatField($field): string {
		if (is_array($field)){
			if (!isset($field[0])) $field = array_values($field);
			return NON_EMPTY_STR(@$this->getProperty($field[0]), @$field[1], @$field[2], @$field[3]);
		}
		else{
			return EMPTY_STR(@$this->getProperty($field), $field); // Or by name if it just text
		}
	}

	/**
	* Clear all settings
	*
	* @return &$this
	**/
	public function &clear(): static {
		$this->__SETS = [];
		return $this;
	}

	/**
	* Return amount of settings.
	*
	* @return integer
	**/
	public function length(): int {
		return \sizeof($this->__SETS);
	}
}
