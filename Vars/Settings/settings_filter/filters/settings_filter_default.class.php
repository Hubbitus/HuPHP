<?
/**
* Default get - filter. If not value (empty of calback) returns default..
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2011, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2011-03-22 16:24
**/

/**
* Default get - filter. If not value (empty of calback) returns default.
**/
class settings_filter_default extends settings_filter_base{
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
	public function __construct($propName, $defaultValue, $emptyCallback = null){
	parent::__construct($propName, null);
	$this->default = $defaultValue;
	// PHP does not allow call 'empty' via call_user_func, threat it as language
	//	construction contrary of function.
		if ($emptyCallback){
		$this->callback_ = $emptyCallback;
		}
		else{
		$this->callback_ = create_function('$var', 'return empty($var);');
		}
	}#__c

	public function apply(&$name, &$value){
		if (call_user_func($this->callback_, $value)){
		$value = $this->default;
		}
	}#m apply
}#c settings_filter_null
?>