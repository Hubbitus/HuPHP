<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings\Filters;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;

/**
* Ignore - filter. Ignore all value and always return null.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2010-11-18 13:43
**/
class SettingsFilterIgnore extends SettingsFilterBase{
	public function __construct(string $propName){
		parent::__construct($propName, null);
	}
	public function apply(&$name, &$value): mixed{
		$value = null;
		return null;
	}
}
