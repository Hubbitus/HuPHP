<?
/**
* Provide easy to use settigns-class for many purpose. Similar array
* of settings, but provide several addition methods, and magic methods
* to be easy done routine tasks, such as get, set, merge and convert to
* string by provided simple format (For more complex formatting {@see class HuFormat}).
*
* @package settings
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0.7
* @created ?2008-05-30 16:08
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
	* @param	array=null	$array Initial values.
	**/
	function __construct(array $possibles, array $array = null){
		$this->properties = $possibles;
		if ($array) $this->mergeSettingsArray($array);
	}#constructor

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function &setSetting($name, $value){
		parent::setSetting($this->checkNamePossible($name, __METHOD__), $value);
	}#m setSetting

	/**
	* Reimplement extended variant to chect setting name possibility.
	* @inheritdoc
	**/
	public function &getProperty($name){
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
		$a = array_keys(REQUIRED_VAR($setArr)); // Variable introduced only for Strict standard check silence: 'Strict Standards: Only variables should be passed by reference'
		array_walk($a, array($this, 'checkNamePossible'), __METHOD__);
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
		if (!in_array($name, $this->properties)) throw new ClassPropertyNotExistsException(EMPTY_STR($walkmethod, $method).': Property "'.$name.'" does NOT exist in ' . get_class($this) . '!');
		return	$name;
	}#m checkNamePossible

	/**
	* Emulate nesting.
	*
	* As we reimplement object to do not have properties itself, instead
	*	define it in $this->properties we should  provide mechanism to emulate
	*	nestiong, to do not mention each time again presented properties.
	* So, with this method we can define in childs new propery
	*	$this->properties_addon and than call this method (in constructor f.e.)
	*	to add new props.
	*
	* So, method MUST be called explicitly. No any magic here!!!
	**/
	public function nesting(){
		//We can't use here nor operatorr + (union), nor array_merge function. We need ADD elements.
		array_splice($this->properties, count($this->properties), 1, $this->properties_addon);
	}#m nesting
}#c settings_check
?>