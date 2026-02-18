<?php
declare(strict_types=1);
/**
* ReadOnly set - filter. Throws VariableReadOnlyException on try change value.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18
* @created 2010-11-18 13:43
**/

namespace Hubbitus\HuPHP\Vars\Settings\Filters;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;
use Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException;

/**
* ReadOnly set - filter. Throws VariableReadOnlyException on try change value.
**/
class SettingsFilterReadonly extends SettingsFilterBase{
	public function __construct(string $propName){
		parent::__construct($propName, null);
	}
	/**
	* @inheritdoc
	* @throws VariableReadOnlyException
	**/
	public function apply(&$name, &$value): mixed{
		throw new VariableReadOnlyException();
	}
}
