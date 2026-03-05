<?php
declare(strict_types=1);

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
**/

namespace Hubbitus\HuPHP\Vars\Settings;

use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;


/**
* Extended variant of settings_check, with check possible options.
* You may easy add any amount of "filters" on get/set property operations
* 	by easy register new filter like:
*	$obj->addGetFilter('testProp', callback $func);
*
* Set filters intended primarily for static transformations like check, conversions etc.
* 	In this case value transformed on set stage once, and stored result as
*	value only (original value dropped).
*
* Get filters Primarily intended for dynamic values, non-deterministic behavior.
*	F.e. to add current datetime to field, check outside params etc...
*
* @uses settings_filter_base
*
* @example settings_filter.example.php
**/
class SettingsFilter extends SettingsCheckStatic {
	protected $__filter_set = [];
	protected $__filter_get = [];

	/**
	* Apply all desired filters and set value.
	**/
	public function &setSetting($name, $value): static {
		foreach ($this->getFilterSet($name) as $filt){
			$filt->apply($name, $value);
		}
		parent::setSetting($name, $value);
		return $this;
	}

	/**
	* Apply all desired filters and return value.
	* Result not cached!
	* @inheritdoc
	**/
	public function &getProperty($name): mixed {
		$val =& parent::getProperty($name);
		foreach ($this->getFilterGet($name) as $filt){
			$filt->apply($name, $val);
		}
		return $val;
	}

	/**
	* Reimplement in more generic form for automatic handle all get/set transformations.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr): void {
		$this->__SETS = array();
		// For our realization just foreach all, now we can simple invoke mergeSettingsArray()
		$this->mergeSettingsArray($setArr);
	}

	/**
	* Reimplement in more generic form for automatic handle all get/set transformations.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr): void {
		/*
		* This may be done also through array_walk, but in it required intermediate function to swap arguments.
		* I think direct cycle will be faster.
		**/
		foreach (REQUIRED_VAR($setArr) as $key => $value)
			$this->setSetting($key, $value);
	}

	/**
	* Add filter into property Get filters queue.
	*
	* @param SettingsFilterBase $filter Filter to add.
	* @return int FilterId to allow delete it later.
	**/
	public function addFilterGet(SettingsFilterBase $filter): int {
		$q = $this->getFilterGet($filter->propName);
		$q->push($filter);
		return ($q->count() - 1);
	}

	/**
	* Add filter into property Set filters queue.
	*
	* @param SettingsFilterBase $filter Filter to add.
	* @return int FilterId to allow delete it later.
	**/
	public function addFilterSet(SettingsFilterBase $filter): int {
		$q = $this->getFilterSet($filter->propName);
		$q->push($filter);
		return ($q->count() - 1);
	}

	/**
	* Base variant of search filter. Compare just by full name of property.
	* Extend class and reimplement getFilterGet()/getFilterSet() methods may be good idea to provide select,
	*	say by part of name, by start, pattern or even by regular expression!
	*
	* @param	string	$name Name of property for what filter search.
	* @return \SplDoublyLinkedList Queue of required filters (may be empty).
	**/
	protected function &getFilterGet($name): \SplDoublyLinkedList {
		if (!isset($this->__filter_get[$name])) {
			$this->__filter_get[$name] = new \SplDoublyLinkedList();
		}
		return $this->__filter_get[$name];
	}

	/**
	* Base variant of search filter. Compare just by full name of property.
	* Extend class and reimplement getFilterGet()/getFilterSet() methods may be good idea to provide select,
	*	say by part of name, by start, pattern or even by regular expression!
	*
	* @param string $name Name of property for what filter search.
	* @return \SplDoublyLinkedList Queue of required filters (may be empty).
	**/
	protected function &getFilterSet($name): \SplDoublyLinkedList{
		if (!isset($this->__filter_set[$name])) $this->__filter_set[$name] = new \SplDoublyLinkedList();
		return $this->__filter_set[$name];
	}

	/**
	* Delete Get filter property from filters queue.
	*
	* @param string $propName Name of property for what filter search.
	* @param int $filterId Filter Id from methods {@see addFilter[GS]et()}
	* @return static
	**/
	public function &delFilterGet($propName, $filterId){
		$this->getFilterGet($propName)->offsetUnset($filterId);;
		return $this;
	}
	/**
	* Delete Set filter property from filters queue.
	*
	* Warning: nothing original values saved, so, delete filter from queue
	*	will affect only nes set operations. All properties now leave as is.
	*
	* @param string $propName Name of property for what filter search.
	* @param int $filterId Filter Id from methods {@see addFilter[GS]et()}
	* @return static
	**/
	public function &delFilterSet($propName, $filterId){
		$this->getFilterSet($propName)->offsetUnset($filterId);;
		return $this;
	}
}
