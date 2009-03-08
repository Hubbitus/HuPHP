<?
/**
* Class to provide OOP interface to array operations.
*
* @package Vars
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-09-22 17:55 ver 1.1 to 1.1.1
*	* Add majority this phpdoc header.
*	- Change include_once('settings.php'); to include_once('Settings/settings.php');
*
*	* 2009-02-27 15:08 ver 1.1.1 to 1.1.2
*	- Some minor fixes in comments.
*
*	* 2009-02-27 17:22 ver 1.1.2 to 1.1.3
*	- Add method filter()
*	- Add support and implementation of Iterator interface
*
*	* 2009-03-02 02:04 ver 1.1.3 to 1.1.4
*	- Add method ::implode()
*	- Add metchod ::count()
*
*	* 2009-03-06 15:29 ver 1.1.4 to 1.1.5
*	- Change include_once('Settings/settings.php'); to include_once('Vars/Settings/settings.php');
*
*	* 2009-03-08 15:31 ver 1.1.5 to 1.2
*	- Add method {@see ::hu()}.
*	- Modified method __get to support construction like: $HuArrayObj->{'hu://varName'}
*	- Add methods ::filterByKeys() and ::filterOutByKeys().
*	- Add method ::filterKeysCallback()
**/

include_once('Vars/Settings/settings.php');

class HuArray extends settings implements Iterator{
const huScheme = 'hu://';

	/**
	* Constructor.
	*
	* @param	(array)mixed=null	$array	 Mixed, explicit cast as array!
	**/
	function __construct(/*(array)*/ $array = null){
	parent::__construct((array)$array);
	}#__c

	/**
	* Push values.
	*
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
	*
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
	*
	* @param 	mixed	$var.
	* @return	$this->pushArray()
	**/
	public function &pushHuArray(HuArray $arr){
	return $this->pushArray($arr->getArray());
	}#m pushHuArray

	/**
	* Return last element in array. Reference, direct-editable!!
	*
	* @return &mixed
	**/
	public function &last(){
	end($this->__SETS);
	return $this->__SETS[key($this->__SETS)];
	}#m last

	/**
	* Return Array representation (cast to (array)).
	*
	* @return	array
	**/
	public function getArray(){
	return $this->__SETS;
	}#m getArray

	/**
	* {@see http://php.net/array_slice}
	*
	* @param	integer	$offset
	*	Если параметр offset положителен, последовательность начнётся на расстоянии offset от начала array. Если offset отрицателен, последовательность начнётся на расстоянии offset от конца.
	* @param	integer	$length
	*	Если в эту функцию передан положительный параметр length, последовательность будет включать length элементов. Если в эту функцию передан отрицательный параметр length, в последовательность войдут все элементы исходного массива, начиная с позиции offset и заканчивая позицией, отстоящей на length элементов от конца. Если этот параметр будет опущен, в последовательность войдут все элементы исходного массива, начиная с позиции offset.
	* @param	boolean	$preserve_keys
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
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name) return $this->last();
		/*
		* Short form of ::hu. To allow constructions like:
		* $obj->{'hu://varName'}->{'hu://0'};
		* instead of directly:
		* $obj->hu('varName')->hu(0);
		* As you like
		**/
		elseif( self::huScheme == substr($name, 0, strlen(self::huScheme)) ) return $this->hu( substr($name, strlen(self::huScheme)) );
		else
		return $this->getProperty($name);
	}#m __get

	/**
	* Like standard {@see __get()}, but if returned value is regular array, convert it into HuArray and return reference to it.
	* @example:
	* $ha = new HuArray(
	*	array(
	*		'one' => 1
	*		,'two' => 2
	*		,'arr' => array(0, 11, 22, 777)
	*	)
	* );
	* dump::a($ha->one);
	* dump::a($ha->arr);					// Result Array (raw, as is)!
	* dump::a($ha->hu('arr'));				// Result HuArray (only if result had to be array, as is otherwise)!!! Original modified in place!
	* dump::a($ha->hu('arr')->hu(2));			// Property access. Alse as any HuArray methods like walk(), filter() and any other.
	* dump::a($ha->{'hu://arr'}->{'hu://2'});	// Alternate method ({@see ::__get()}). Fully equivalent of line before. Just another form.
	*
	* @param	mixed	$name
	* @return	&mixed
	**/
	function &hu($name){
		if (is_array($this->$name)) $this->$name = new HuArray($this->$name);
	return $this->getProperty($name);
	}#m hu

	/**
	* Allow change value by short direct form->setttingName = 'qwerty';
	*
	* @param	string	$name
	* @param	mixed	$value
	**/
	function __set($name, $value){
		/**
		* Needed name, because $var->last() = 'NewVal' produce error, even if value returned by reference: 
		* PHP Fatal error:  Can't use method return value in write context in /var/www/_SHARED_/Console/HuGetopt.php on line 233
		**/
		if ('_last_' == $name){
		$ref =& $this->last();
		}
		else{
		$ref =& $this->getProperty($name);
		}
	$ref = $value;
	}#m __set

	/**
	* Apply callback function to each element.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function walk($callback){
	array_walk($this->__SETS, $callback);
	return $this;
	}#m walk

	/**
	* Filter array, using callback. If the callback function returns true, the current value from input is returned into the result
	* array. Array keys are preserved.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function &filter($callback){
	$this->__SETS = array_filter($this->__SETS, $callback);
	return $this;
	}#m filter

	/**
	* Filter array by keys and leave only mentioned in $keys array
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterByKeys(array $keys){
	$this->__SETS = array_diff_key( $this->__SETS, array_flip(  array_intersect(   array_keys($this->__SETS), $keys   )  ) );
	return $this;
	}#m filterByKeys

	/**
	* Filter array by keys and leave only NOT mentioned in $keys array (opposite to method {@see ::filterByKeys()})
	*
	* Implementation idea taken from: http://ru.php.net/array_filter comment of niehztog
	*
	* @param	array	$keys
	* @return	&$this
	**/
	public function &filterOutByKeys(array $keys){
	$this->__SETS = array_diff_key( $this->__SETS, array_flip($keys) );
	return $this;
	}#m filterOutByKeys

	/**
	* Similar to {@see ::filer()} except of operate by keys instead of values.
	*
	* @param	callback	$callback
	* @return	&$this
	**/
	public function &filterKeysCallback($callback){
	$keys = new self(array_flip( $this->__SETS ));
	$keys->filter($callback);
	$this->filterByKeys($keys->getArray());
	return $this;
	}#m filterKeysCallback

	/**
	* Implode to the string using provided delimiter.
	*
	* @param	string	$delim
	* @return	string
	**/
	public function implode($delim){
	return implode($delim, $this->__SETS);
	}#m implode

	/**
	* Return number of elements
	*
	* @return	int
	**/
	public function count(){
	return count($this->__SETS);
	}#m count

/*##########################################################
## From interface Iterator
##########################################################*/
	public function rewind(){
	reset($this->__SETS);
	}#m rewind

	public function current(){
	return /* $var = */ current($this->__SETS);
	}#m current

	public function key(){
	return /* $var = */ key($this->__SETS);
	}#m key

	public function next(){
	return /* $var =*/ next($this->__SETS);
	}#m next

	public function valid(){
	return ($this->current() !== false);
	}#m valid
}#c HuArray
?>