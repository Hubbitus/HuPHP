<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use Hubbitus\HuPHP\Macro\Vars;

/**
* Extended variant of {@see Settings}, with check possible options.
* Slowly, but safely.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created ?2008-05-30 16:08
**/
class SettingsCheck extends Settings {
	public $properties = [];

	/**
	* Constructor.
	*
	* @param array $possibles Array of string - possible names of properties.
	* @param array|null $array Initial values.
	**/
	public function __construct(array $possibles, ?array $array = null) {
		// Support both numeric and associative arrays for $possibles
		// Numeric: ['name', 'age'] => use values as property names
		// Associative: ['name' => null, 'age' => null] => use keys as property names
		$this->properties = \array_keys($possibles);

		// If numeric array, use values as property names
		if ($this->properties === \range(0, \count($this->properties) - 1)) {
			$this->properties = $possibles;
		}

		parent::__construct();
		if ($array !== null && $array !== []) {
			$this->mergeSettingsArray($array);
		}
	}

	/**
	* Reimplement extended variant to check setting name possibility.
	* @inheritdoc
	**/
	public function &setSetting($name, $value): static {
		parent::setSetting($this->checkNamePossible($name, __METHOD__), $value);
		return $this;
	}

	/**
	* Reimplement extended variant to check setting name possibility.
	* @inheritdoc
	**/
	public function &getProperty($name): mixed {
		return parent::getProperty($this->checkNamePossible($name, __METHOD__));
	}

	/**
	* Add setting with value in possible settings.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	void
	**/
	public function addSetting($name, $value): void {
		$this->properties[] = $name;
		parent::setSetting($name, $value);
	}

	/**
	* Reimplement extended variant to check setting name possibility.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr): void {
		$keys = \array_keys(Vars::requiredNotEmpty($setArr));
		\array_walk($keys, [$this, 'checkNamePossible'], __METHOD__);
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
		$a = \array_keys(Vars::requiredNotEmpty($setArr)); // Variable introduced only for Strict standard check silence: 'Strict Standards: Only variables should be passed by reference'
		\array_walk($a, [$this, 'checkNamePossible'], __METHOD__);
		parent::mergeSettingsArray($setArr);
	}

	/**
	* Check if name is possible, and Throw(ClassPropertyNotExistsException) if not.
	*
	* @param	string	$name. Name to check.
	* @param	string	$method. To Exception - caller method name.
	* @param	string	$walkmethod. Only for array_walk compatibility - it is must be 3d parameter.
	* @return	string	$name
	* @throws ClassPropertyNotExistsException
	**/
	protected function checkNamePossible($name, $method, $walkmethod = null): string {
		if (!\in_array($name, $this->properties, true)) {
			throw new ClassPropertyNotExistsException(
				Vars::firstMeaningString($walkmethod, $method)
				. ': Property "'
				. $name
				. '" does NOT exist in '
				. \get_class($this)
				. '!'
			);
		}
		return	$name;
	}

	/** @var array */
	public $properties_addon = [];

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
	public function nesting(): void {
		//We can't use here nor operator + (union), nor array_merge function. We need ADD elements.
		\array_splice($this->properties, \count($this->properties), 1, $this->properties_addon);
	}
}
