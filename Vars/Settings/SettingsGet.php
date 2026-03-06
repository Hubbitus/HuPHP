<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

/**
* Parent class for more useful using in parents who want be "customizable". Convenient nesting.
**/
class SettingsGet {
/** WARNING! Must be initialized in parents! **/
protected ?array $_sets = null;

	/**
	* Overload to provide ref on settings object. So, settings will be changeable,
	* but can't be replaced settings object!
	*
	* @param string $name
	* @return ?mixed
	**/
	public function &__get ($name): mixed {
		if ('settings' === $name) {
			return $this->_sets;
		}
		return $this->_sets[$name];
	}

	/**
	* Return settings object
	*
	* @return ?array
	**/
	public function &sets(): ?array {
		return $this->_sets;
	}
}
