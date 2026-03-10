<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings\Filters;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;

/**
* Default get - filter. If not value (empty of callback) returns default.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2011-03-22 16:24
**/
class SettingsFilterDefault extends SettingsFilterBase {
	private mixed $default;
	private \Closure $callback_;

	/**
	* If property empty (check by call $emptyCallback) return default value.
	*
	* @param string $propName
	* @param mixed $defaultValue
	* @param callable|null $emptyCallback Should behave as empty() standard
	*   function - accept 1 argument and returns true if argument considered 'empty'.
	*   By default - null, then empty construction used itself.
	**/
	public function __construct(string $propName, mixed $defaultValue, ?callable $emptyCallback = null) {
		parent::__construct($propName, null);
		$this->default = $defaultValue;
		$this->callback_ = $emptyCallback ?? fn($var) => $var === null || $var === '' || $var === [] || $var === false || $var === 0;
	}

	public function apply(&$name, &$value): mixed {
		if ((bool)\call_user_func($this->callback_, $value)) {
			$value = $this->default;
		}
		return $value;
	}
}
