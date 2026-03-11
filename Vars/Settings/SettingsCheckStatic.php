<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

/**
* Extended variant of {@see SettingsCheck} to handle "uncleared" fields.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
**/
class SettingsCheckStatic extends SettingsCheck {
	protected $static_settings = [];

	/**
	* Clear all except uncleared items.
	**/
	public function &clear(): static {
		foreach ($this->getRegularKeys() as $key) {
			$this->__SETS[$key] = null;
		}
		return $this;
	}

	/**
	* Return array of regular keys, without 'uncleared' (private, static)
	*
	* @return	array
	**/
	public function getRegularKeys(): array {
		return \array_diff(\array_keys($this->__SETS), $this->static_settings);
	}
}
