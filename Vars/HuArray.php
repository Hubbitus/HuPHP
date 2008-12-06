<?
/**
* 
* Class to provide OOP interface to array operations.
*
* @package Vars
* @version 1.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-09-22 17:55 ver 1.1 to 1.1.1
*	* Add majority this phpdoc header.
*	- Change include_once('settings.php'); to include_once('Settings/settings.php');
**/

include_once('Settings/settings.php');

class HuArray extends settings{
	/**
	* Constructor.
	* @param	(array)mixed=null	$array	 Mixed, explicit cast as array!
	**/
	function __construct(/*(array)*/ $array = null){
	parent::__construct((array)$array);
	}#constructor

	/**
	* Push values.
	* @param 	mixed	$var.
	* @params	mixed	any amount of vars (First explicity to make mandatory one at once)
	* @return	&$this
	**/
	public function &push($var){
	call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), func_get_args()));
	return $this;
	}#m push

	/**
	* Push array of values.
	* @param 	array	$arr
	* @return	&$this
	**/
	public function &pushArray(array $arr){
		if ($arr)
		call_user_func_array('array_push', array_merge(array(0 => &$this->__SETS), $arr));
	return $this;
	}#m pushArray

	/**
	* Push values from Object(HuArray).
	* @param 	mixed	$var.
	* @return	$this->pushArray()
	**/
	public function &pushHuArray(HuArray $arr){
	return $this->pushArray($arr->getArray());
	}#m pushHuArray

	/**
	* Return last element in array. Reference, direct-editable!!
	* @return &mixed
	**/
	public function &last(){
	end($this->__SETS);
	return $this->__SETS[key($this->__SETS)];
	}#m last

	/**
	* Return Array representation (cast to (array)).
	* @return	array
	**/
	public function getArray(){
	return $this->__SETS;
	}#m getArray

	/**
	* {@see http://php.net/array_slice}
	* @param integer	$offset
	*	Если параметр offset положителен, последовательность начнётся на расстоянии offset от начала array. Если offset отрицателен, последовательность начнётся на расстоянии offset от конца.
	* @param integer	$length
	*	Если в эту функцию передан положительный параметр length, последовательность будет включать length элементов. Если в эту функцию передан отрицательный параметр length, в последовательность войдут все элементы исходного массива, начиная с позиции offset и заканчивая позицией, отстоящей на length элементов от конца. Если этот параметр будет опущен, в последовательность войдут все элементы исходного массива, начиная с позиции offset.
	* @param boolean	$preserve_keys
	*	Обратите внимание, поумолчанию сбрасываются ключи массива. Можно переопределить это поведение, установив параметр preserve_keys в TRUE. 
	* @return Object(HuArray)
	**/
	public function getSlice($offset, $length = null, $preserve_keys = false){
	return new HuArray(array_slice($this->__SETS, $offset, EMPTY_VAR($length, sizeof($this->__SETS)), $preserve_keys));
	}#m getSlice

	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	public function &getProperty($name){
	return $this->__SETS[REQUIRED_NOT_NULL($name)];
	}#m getProperty

	/**
	* @var	&mixed	->_last_
	**/
	/**
	* Overload to return reference.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	function &__get($name){
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference: 
		PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		*/
		if ('_last_' == $name) return $this->last();
	return $this->getProperty($name);
	}#m __get

	/**
	* Allow change value by short direct form->setttingName = 'qwerty';
	*
	* @param string	$name
	* @param mixed	$value
	*/
	function __set($name, $value){
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference: 
		PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		*/
		if ('_last_' == $name){
		$ref =& $this->last();
		}
		else{
		$ref =& $this->getProperty($name);
		}
	$ref = $value;
	}#m __set

	/**
	* Apply callback function to yeach element.
	* @return &$this
	**/
	public function walk($callback){
	array_walk($this->__SETS, $callback);
	return $this;
	}#m walk
}#c HuArray
?>