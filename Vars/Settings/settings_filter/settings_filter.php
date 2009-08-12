<?
/**
* Extends settings_check to allow apply get/set filters.
* It may be constraints check (f.e. check and throw exception on error),
*	and/or any modifications like clear user input, convert formats and etc.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-29
*
* @changelog
**/


/**
* Extended variant of settings_check, with check possible options.
* You may easy add any amount of "filters" on get/set property operations
* 	by easy register new filter like:
*	$obj->addGetFilter('testProp', callback $func);
*
* Set filters intended prmarly for static transformations like check, conversions etc.
* 	In this case value transformed on set stage once, and stored result as
*	value only (original value droped).
*
* Get filters primarly intended for dinamic values, non-deterministic behaviour.
*	F.e. to add current datetime to field, check outside params etc...
*
* @uses settings_filter_base
*
* @example settings_filter.example.php
**/
class settings_filter extends settings_check_static{
protected $__filt_set = array();
protected $__filt_get = array();
	/**
	* Apply all desired filters and set value.
	**/
	public function setSetting($name, $value){
		foreach ($this->getFilterSet($name) as $filt){
		$value = $filt->apply($name, $value);
		}
	parent::setSetting($name, $value);
	}#m setSetting

	/**
	* Apply all desired filters and return value.
	* Result not chached!
	* @inheritdoc
	**/
	public function &getProperty($name){
	$val =& parent::getProperty($name);
		foreach ($this->getFilterGet($name) as $filt){
		$val = $filt->apply($name, $val);
		}
	return $val;
	}#m getProperty

	/**
	* Reimplemnt in more generic form for automatic handle all get/set transformations.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr){
	$this->__SETS = array();
	// For our realisation just foreach all, now we can simple invoke mergeSettingsArray()
	$this->mergeSettingsArray($setArr);
	}#m setSettingsArray

	/**
	* Reimplemnt in more generic form for automatic handle all get/set transformations.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
		/*
		* This may be done also through array_walk, but in it required intermediate function to swap arguments.
		* I think direct cycle will be faster.
		**/
		foreach (REQUIRED_VAR($setArr) as $key => $value) $this->setSetting($key, $value);
	}#m mergeSettingsArray

	/**
	* Add filter into property Get filters queue.
	*
	* @param	Object(settings_filter_base)	$filt. Filter to add.
	* @return	integer.	FilterId to allow delete it later.
	**/
	public function addFilterGet(settings_filter_base $filt){
	$q = $this->getFilterGet($filt->propName);
	$q->push($filt);
	return ($q->count() - 1);
	}#m addFilterGet

	/**
	* Add filter into property Set filters queue.
	*
	* @param	Object(settings_filter_base)	$filt. Filter to add.
	* @return	integer.	FilterId to allow delete it later.
	**/
	public function addFilterSet(settings_filter_base $filt){
	$q = $this->getFilterSet($filt->propName);
	$q->push($filt);
	return ($q->count() - 1);
	}#m addFilterSet

	/**
	* Base variant of search feilter. Compare just by full name of property.
	* Extend class and reimplement getFilterGet()/getFilterSet() methods may be good idea to provide select,
	*	say by part of name, by start, pattern or even by regular expression!
	*
	* @param	string	$name Name ofproperty for what filter search.
	* @return	&Object(SplDoublyLinkedList) Queue of required filters (may be empty).
	**/
	protected function &getFilterGet($name){
		if (!isset($this->__filt_get[$name])) $this->__filt_get[$name] = new SplDoublyLinkedList();
	return $this->__filt_get[$name];
	}#m getFilterGet

	/**
	* Base variant of search feilter. Compare just by full name of property.
	* Extend class and reimplement getFilterGet()/getFilterSet() methods may be good idea to provide select,
	*	say by part of name, by start, pattern or even by regular expression!
	*
	* @param	string	$name Name of property for what filter search.
	* @return	&Object(SplDoublyLinkedList) Queue of required filters (may be empty).
	**/
	protected function &getFilterSet($name){
		if (!isset($this->__filt_set[$name])) $this->__filt_set[$name] = new SplDoublyLinkedList();
	return $this->__filt_set[$name];
	}#m getFilterSet

	/** @TODO. Implement RAW-functionality in child class
	* If for property registered at least one filter vith private flag, all property turn to private, and
	*	requesting its raw value caused exception
	public function getRaw($name){
		if(!)
	}#m getRaw
	**/

	/**
	* Delete Get filter property from filters queue.
	*
	* @param	string	$name Name of property for what filter search.
	* @param	integer	$filterId Filter Id from methods {@see addFilter[GS]et()}
	* @return	&$this
	**/
	public function &delFilterGet($propName, $filterId){
	$this->getFilterGet($propName)->offsetUnset($filterId);;
	return $this;
	}#m delFilterGet

	/**
	* Delete Set filter property from filters queue.
	*
	* Warning: nothing original values saved, so, delete filter from queue
	*	will affect only nes set operations. All properties now leave as is.
	*
	* @param	string	$name Name of property for what filter search.
	* @param	integer	$filterId Filter Id from methods {@see addFilter[GS]et()}
	* @return	&$this
	**/
	public function &delFilterSet($propName, $filterId){
	$this->getFilterSet($propName)->offsetUnset($filterId);;
	return $this;
	}#m delFilterSet
}#c settings_filter
?>