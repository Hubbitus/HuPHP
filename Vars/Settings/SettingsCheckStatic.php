<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings;

/**
* Extended variant of {@see SettingsCheck} to handle "uncleared" fields.
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created ???
**/

/**
* Extended variant of settings_check to handle "uncleared" fields.
**/
class SettingsCheckStatic extends SettingsCheck {
	protected $static_settings = [];
	/**
	* Clear all except uncleared items.
	**/
	public function clear(): void {
		foreach ($this->getRegularKeys() as $key => $sets){
			$this->__SETS[$key] = null;
		}
	}
	/**
	* Return array of regular keys, without 'uncleared' (private, static)
	*
	* @return	array
	**/
	public function getRegularKeys(): array {
		return array_diff(array_keys($this->__SETS), $this->static_settings);
	}
}
