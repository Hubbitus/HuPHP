<?
/**
* Extended variant of settings_check to handle "uncleared" fields.
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created ???
**/

/**
* Extended variant of settings_check to handle "uncleared" fields.
**/
class settings_check_static extends settings_check{
protected $static_settings = array();
	/**
	* Clear all except uncleared items.
	**/
	public function clear(){
		foreach ($this->getRegularKeys() as $key => $sets){
		$this->__SETS[$key] = null;
		}
	}#m clear

	/**
	* Return array of regular keys, without 'uncleared' (private, static)
	*
	* @return	array
	**/
	public function getRegularKeys(){
	return array_diff(array_keys($this->__SETS), $this->static_settings);
	}#m getRegularKeys
}#c settings_check_static
?>