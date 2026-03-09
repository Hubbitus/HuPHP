<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Settings\Filters;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;
use Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException;

/**
* ReadOnly set - filter. Throws VariableReadOnlyException on try change value.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2010-11-18 13:43
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
