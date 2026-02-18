<?php
declare(strict_types=1);
/**
* Default get - filter. If not value (empty of callback) returns default.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2011, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2011-03-22 16:24
**/

namespace Hubbitus\HuPHP\Vars\Settings\Filters;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;

/**
* Default get - filter. If not value (empty of callback) returns default.
**/
class SettingsFilterDefault extends SettingsFilterBase {
private $default;
private $callback_;
	/**
	 * If property empty (check by call $emptyCallback) return default value.
	 *
	 * @param	string	$propName
	 * @param	mixed	$defaultValue
	 * @param	callback(null)	$emptyCallback. Should behave as empty() standard
	 *	function - accept 1 argument and returns true if argument considered 'empty'.
	 *	By default - null, then empty construction used itself.
	 */
	public function __construct(string $propName, mixed $defaultValue, ?callable $emptyCallback = null){
		parent::__construct($propName, null);
		$this->default = $defaultValue;
		// PHP does not allow call 'empty' via call_user_func, threat it as language
		//	construction contrary of function.
		if ($emptyCallback){
			$this->callback_ = $emptyCallback;
		}
		else{
			$this->callback_ = fn($var) => empty($var);
		}
	}
	public function apply(&$name, &$value): mixed{
		if (call_user_func($this->callback_, $value)){
			$value = $this->default;
		}
		return $value;
	}
}
