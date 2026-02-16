<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;

/**
* Extended variant of {@see Settings}, with check possible options.
* Slowly, but safely.
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0.7
* @created ?2008-05-30 16:08
**/
class SettingsCheck extends Settings {
	public $properties = [];

	/**
	* Constructor.
	*
	* @param	array	$possibles. Array of string - possible names of properties.
	* @param	array=null	$array Initial values.
	**/
	public function __construct(array $possibles, ?array $array = null){
		$this->properties = $possibles;
		if ($array) $this->mergeSettingsArray($array);
	}

	/**
	* Reimplement extended variant to check setting name possibility.
	* @inheritdoc
	**/
	public function &setSetting($name, $value): static {
		parent::setSetting($this->checkNamePossible($name, __METHOD__), $value);
	}

	/**
	* Reimplement extended variant to check setting name possibility.
	* @inheritdoc
	**/
	public function &getProperty($name){
		return parent::getProperty($this->checkNamePossible($name, __METHOD__));
	}

	/**
	* Add setting with value in possible settings.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	void
	**/
	public function addSetting($name, $value){
		$this->properties[] = $name;
		parent::setSetting($name, $value);
	}

	/**
	* Reimplement extended variant to check setting name possibility.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr): void {
		array_walk(array_keys(REQUIRED_VAR($setArr)), array($this, 'checkNamePossible'), __METHOD__);
		parent::setSettingsArray($setArr);
	}

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of required property
	* @return	boolean
	**/
	public function __isset($name): bool {
		return parent::__isset($this->checkNamePossible($name, __METHOD__));
	}

	/**
	* Reimplement extended variant to check setting name possibility.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr): void {
		$a = array_keys(REQUIRED_VAR($setArr)); // Variable introduced only for Strict standard check silence: 'Strict Standards: Only variables should be passed by reference'
		array_walk($a, array($this, 'checkNamePossible'), __METHOD__);
		parent::mergeSettingsArray($setArr);
	}

	/**
	* Check if name is possible, and Throw(ClassPropertyNotExistsException) if not.
	*
	* @param	string	$name. Name to check.
	* @param	string	$method. To Exception - caller method name.
	* @param	string	$walkmethod. Only for array_walk compatibility - it is must be 3d parameter.
	* @return	string	$name
	* @Throws	(ClassPropertyNotExistsException)
	**/
	protected function checkNamePossible($name, $method, $walkmethod = null){
		if (!in_array($name, $this->properties)) throw new ClassPropertyNotExistsException(EMPTY_STR($walkmethod, $method).': Property "'.$name.'" does NOT exist in ' . get_class($this) . '!');
		return	$name;
	}

	/**
	* Emulate nesting.
	*
	* As we reimplement object to do not have properties itself, instead
	*	define it in $this->properties we should  provide mechanism to emulate
	*	nesting, to do not mention each time again presented properties.
	* So, with this method we can define in children new property
	*	$this->properties_addon and than call this method (in constructor f.e.)
	*	to add new props.
	*
	* So, method MUST be called explicitly. No any magic here!!!
	**/
	public function nesting(){
		//We can't use here nor operator + (union), nor array_merge function. We need ADD elements.
		array_splice($this->properties, count($this->properties), 1, $this->properties_addon);
	}
}
