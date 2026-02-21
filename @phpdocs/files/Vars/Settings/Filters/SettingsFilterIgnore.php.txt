<?php
declare(strict_types=1);
/**
* Ignore - filter. Ignore all value and always return null.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18 13:43
**/

namespace Hubbitus\HuPHP\Vars\Settings\Filters;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;

/**
* Ignore - filter. Ignore all value and always return null.
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
