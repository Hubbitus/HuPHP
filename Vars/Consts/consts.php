<?
/**
* Constants manipulation
*
* @package Vars
* @subpackage Consts
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-29 17:45 ver 1.0 to 1.0.1
**/

/**
* @example consts.example.php
**/
class consts{ // constants
	/**
	* Возвращает массив констант
	*
	* @param	string	Category of constants needed.
	* @param	string	Regexp to filter out. Default "@.*@i", what meen - no filter, return all.
	* @param	boolean	True, if want do NOT categorize items
	* @return	array	Associative array of matched constants with its values.
	*/
	public static function get_regexp($category='', $regexp='@.*@i', $not_categorized=false){
		// It seems just presents of argument checked, without of dependency of it value (true, false and null probed)
		if ($not_categorized) $constants = get_defined_constants($not_categorized);
		else $constants = get_defined_constants();

		$consts = ( ($not_categorized or empty($category))? $constants : $constants[$category] );
		$new_consts = array();
		if (is_array(reset($consts))){
			foreach ($consts as $key => $c_arr){
				$new_c_arr = @array_flip (preg_grep ( $regexp, array_flip($c_arr) ));
				if ( ! empty($new_c_arr) ) $new_consts[$key] = $new_c_arr;
			}
		}
		else{
			$new_consts = @array_flip (preg_grep ( $regexp, array_flip($consts) ));
		}

		return $new_consts;
	}#m get_regexp

	/**
	* Return pair Constant-name and it values
	*
	* @param	string Constant name.
	* @return array Associative array with key of constant-name, and value it value
	*/
	public static function get($const){
		return array($const => constant($const));
	}#m get

	/**
	* Locate constant-name by its value.
	*
	* @param mixed	$value - needed value
	* @param	string	Category of constants needed. {@see ::get_regexp}
	* @param	string	Regexp to filter out. Default "@.*@i", what meen - no filter, return all. {@see ::get_regexp}
	* @param	boolean	True, if want do NOT categorize items {@see ::get_regexp}
	* @return	array	Associative array of matched constants with its values.
	**/
	public static function getNameByValue($value, $category='', $regexp='@.*@i', $not_categorized=false){
		$constants = self::get_regexp($category, $regexp, $not_categorized);
		$cmp = new const_value_filter($value);

		if (!is_array(current($constants)))
			return array_filter($constants, array($cmp, 'cmp'));
		else{
			foreach ($constants as $key => &$arr){
				$constants[$key] = array_filter($constants[$key], array($cmp, 'cmp'));
			}
			return array_filter($constants);
		}
	}#m getNameByValue
}#c consts

/*
* Due to:
* PHP Fatal error:  Class declarations may not be nested in ...
* it helper-class must be defined in global scope.
**/
class const_value_filter{
	private $_val;

	function __construct(&$val){
		$this->_val =& $val;
	}#__c

	function cmp(&$item){
		return ($this->_val == $item);
	}#m cmp
}#c
?>