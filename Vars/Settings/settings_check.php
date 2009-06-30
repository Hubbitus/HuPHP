<?
/**
* Provide easy to use settigns-cllass for many purpose. Similar array
* of settings, but provide several addition methods, and magick methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see
* class HuFormat}).
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0.6
*
* @changelog
*	* 2008-05-30 16:08
*	- Add compatibility with PHP < 5.3.0. Replace "static::$properties" to "self::$properties"
*	 in case when it defined and used in one class is it correct.
*
*	* 2008-09-22 17:44 ver 1.0.1 to 1.0.2
*	- Change include_once('settings.php'); to include_once('Settings/settings.php');
*
*	* 2009-03-01 14:55 ver 1.0.2 to 1.0.3
*	- Method checkNamePossible() changed from private to protected (Primarly for Config class)
*
*	* 2009-03-06 15:29 ver 1.0.3 to 1.0.4
*	- Change include_once('Settings/settings.php'); to include_once('Vars/Settings/settings.php');
*
*	* 2009-03-10 04:24 ver 1.0.4 to 1.0.5
*	- Add method ::addSetting()
*
*	* 2009-05-21 22:46 ver 1.0.5 to 1.0.6
*	* Make $properties non-static! This prevent mesh properies in 2 child of this class.
**/

include_once('Exceptions/classes.php');
include_once('Vars/Settings/settings.php');

/**
* Extended variant of settings, with check possible options.
* Slowly, but safely.
**/
class settings_check extends settings{
public $properties = array();

	/**
	* Constructor.
	*
	* @param	array	$possibles. Array of string - possibe names of propertys.
	* @param	array=null $array
	**/
	function __construct(array $possibles, array $array = null){
	$this->properties = $possibles;
		if ($array) $this->mergeSettingsArray($array);
	}#constructor

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function setSetting($name, $value){
	parent::setSetting($this->checkNamePossible($name, __METHOD__), $value);
	}#m setSetting

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function getProperty($name){
	return parent::getProperty($this->checkNamePossible($name, __METHOD__));
	}#m getProperty

	/**
	* Add setting vith value in possible settings.
	*
	* @param	string	$name
	* @param	mixed	$value
	* @return	nothing
	**/
	public function addSetting($name, $value){
	$this->properties[] = $name;
	parent::setSetting($name, $value);
	}#m addSetting

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function setSettingsArray(array $setArr){
	array_walk(array_keys(REQUIRED_VAR($setArr)), array($this, 'checkNamePossible'), __METHOD__);
	parent::setSettingsArray($setArr);
	}#m setSettingsArray

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param	string	$name	Name of required property
	* @return	boolean
	**/
	public function __isset($name) {
	return parent::__isset($this->checkNamePossible($name, __METHOD__));
	}#m __isset

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
	array_walk(array_keys(REQUIRED_VAR($setArr)), array($this, 'checkNamePossible'), __METHOD__);
	parent::mergeSettingsArray($setArr);
	}#m mergeSettingsArray

	/**
	* Check if name is possible, and Throw(ClassPropertyNotExistsException) if not.
	*
	* @param	string	$name. Name to check.
	* @param	string	$method. To Exception - caller method name.
	* @param	string	$walkmethod. Only for array_walk compatibility - it is must be 3d parameter.
	* @return	string	$name
	* @Throws	(ClassPropertyNotExistsException)
	**/
	protected function checkNamePossible($name, $method, $walkmethod = null){
		if (!in_array($name, $this->properties)) throw new ClassPropertyNotExistsException(EMPTY_STR($walkmethod, $method).': Property "'.$name.'" does NOT exist!');
	return	$name;
	}#m checkNamePossible
}#c settings_check
?>