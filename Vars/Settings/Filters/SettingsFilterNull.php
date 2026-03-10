<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings\Filters;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;

/**
* Null - filter. Return value "AS IS".
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2010-11-18 13:43
**/
class SettingsFilterNull extends SettingsFilterBase {
	/**
	* Only one argument required.
	**/
	public function __construct(string $propName) {
		parent::__construct($propName, null);
	}

	public function apply(&$name, &$value): mixed {
		return null;
	}
}
