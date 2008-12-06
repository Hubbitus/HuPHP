<?
/**
* 
* @package Vars
* @subpackage Consts
* @version 1.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-29 17:45 Version 2.3 from 2.2.b
* - Move examples into separate file
**/

/**
* @example consts.example.php
**/
class consts{#Full - constants
	/**
	* Возвращает массив констант
	*
	* @param	string	Category of constants needed.
	* @param	string	Regexp to filter out. Default "#.*#i", what meen - no filter, return all.
	* @param	boolean	True, if want do NOT categorize items
	* @return	array	Associative array of matched constants with its values.
	*/
	public function get_regexp($category='', $regexp='#.*#i', $not_categorized=false){
	$constants = get_defined_constants($not_categorized);
	$consts = ( ($not_categorized or empty($category))? $constants : $constants[$category] );
	$new_consts = array();
		if (is_array(reset($consts))){
			foreach ($consts as $key => $c_arr){
			$new_c_arr = @array_flip (preg_grep ( $regexp, array_flip($c_arr) ));
			//$new_c_arr = array_flip($c_arr);
				if ( ! empty($new_c_arr) ) $new_consts[$key] = $new_c_arr;
			}
		}
		else{
		//$new_consts = $consts;
		$new_consts = @array_flip (preg_grep ( $regexp, array_flip($consts) ));
		}

	return $new_consts;
	}# m get_regexp

	/**
	* Return pair Constant-name and it values
	*
	* @param	string Constant name.
	* @return array Associative array with key of constant-name, and value it value
	*/
	function get($const){
	return array($const => constant($const));
	}#m get
} #c consts
?>