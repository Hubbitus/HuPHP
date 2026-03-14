<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\Vars\HuClass;

/**
* Provide easy to use settings-class for many purpose. Similar array of settings, but provide several addition magic methods,
* to be easy done routine tasks, such as get, set, merge and convert to string by provided simple format (For more complex formatting {@see HuFormat} class).
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2008-05-30 23:19
**/
class Settings extends HuClass {
	protected array $__SETS = []; // Array of settings itself

	/**
	* Constructor.
	*
	* @param array $array
	**/
	public function __construct(array $array = []) {
		if ($array !== []) {
			$this->mergeSettingsArray($array);
		}
	}

	/**
	* Set setting by its name.
	*
	* @param string $name
	* @param mixed $value
	**/
	public function &setSetting(string $name, mixed $value): static {
		$this->__SETS[$name] = $value;
		return $this;
	}

	/**
	* Rewrite ALL settings. To change only needed - use {@see ::setSetting()} method
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param array $setArr
	**/
	public function setSettingsArray(array $setArr): void {
		$this->__SETS = Vars::requiredNotEmpty($setArr);
	}

	/**
	* Rewrite provided settings by its values. To change single setting you may use {@see ::setSetting()}
	*
	* It will be gracefully if we can turn it into {@see ::setSettings()}, but overloading is not supported in PHP :(
	*
	* @param array $setArr
	**/
	public function mergeSettingsArray(array $setArr): void {
		/**
		* @internal
		* We don't use array_merge there because want preserve keys, even numerical:
		* http://ru2.php.net/manual/en/function.array-merge.php#92602
		* We also can't use simple array concatenation because want overwrite old values by new one...
		* So, doing all manually!
		**/
		foreach (Vars::requiredNotEmpty($setArr) as $key => $val) {
			$this->__SETS[$key] = $val;
		}
	}

	/**
	* Return requested property by name. For more useful access see {@see ::__get()} method.
	*
	* @param string $name
	**/
	public function &getProperty($name): mixed {
		return $this->__SETS[Vars::requiredNotNull($name)];
	}

	/**
	* useful alias of {@see ::setSetting()} to provide easy access in style of $obj->PropertyName = 'Some new value';
	*
	* @param string $name
	* @param mixed $value
	**/
	public function __set(string $name, mixed $value): void {
		$this->setSetting($name, $value);
	}

	/**
	* useful alias of {@see ::getProperty()} to provide easy access in style of $obj->PropertyName
	*
	* @param string $name
	**/
	public function &__get(string $name): mixed {
		return $this->getProperty($name);
	}

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param string $name Name of requested property
	**/
	public function __isset(string $name): bool {
		return isset($this->__SETS[Vars::requiredNotNull($name)]);
	}

	/**
	* Get string representation of settings.
	* @see ::formatField()
	* @param array $fields
	**/
	public function getString(array $fields): string {
		$str = '';
		foreach (Vars::requiredNotEmpty($fields) as $field) {
			$str .= $this->formatField($field);
		}
		return $str;
	}

	/**
	* Format Field Primarily for {@see ::getString}, but may be used separately too
	* $field should be one of the:
	*	1) Property name. If it present and not empty - will be used. Если найдена такая настройка и она не пуста, подставляется она
	*	2) Constant string (no property found by it) - used "as is"
	*	3) Array with structure:
	*	[
	*		'str' => propertyName
	*		'prefix' => ''
	*		'suffix' => ''
	*		'defValue' => ''
	*	]
	*	Note. Array may be uses with keys and without, for simplicity of calling. So both variants are equivalents:
	*	- ['str' => 'tag', 'prefix' => '<', 'suffix' => '>', 'defValue' => '<unknown>']
	*	- ['tag', '<', '>', '<unknown>']
	*
	* @param array|string $field
	**/
	public function formatField(array|string $field): string {
		if (\is_array($field)) {
			if (!isset($field[0])) {
				$field = \array_values($field);
			}
			return Vars::surround(
				@$this->getProperty($field[0]),
				@$field[1],
				@$field[2],
				@$field[3]
			);
		}
		else {
			return Vars::firstMeaningString(@$this->getProperty($field), $field); // Or by name if it just text
		}
	}

	/**
	* Clear all settings
	**/
	public function &clear(): static {
		$this->__SETS = [];
		return $this;
	}

	/**
	* Return amount of settings.
	**/
	public function length(): int {
		return \sizeof($this->__SETS);
	}

	/**
	* Return debug info for var_dump() and similar functions.
	*
	* @return array<string, mixed>
	**/
	public function __debugInfo(): array {
		return $this->__SETS;
	}
}
