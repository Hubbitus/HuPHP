<?
/**
* Debug and backtrace toolkit.
* @package Debug
* @version 2.0b
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
* 2008-05-31 03:19
*	- Add capability to PHP < 5.3.0-dev:
*		* Replace construction ($var ?: "text") with macros EMPTY_STR
* 2008-05-25 17:26
*	- Change include_once('settings.php'); to include_once('Settings/settings.php');
**/

include_once('Settings/settings.php');
include_once('macroses/EMPTY_VAR.php');
include_once('Debug/debug.php');
include_once('System/OS.php');
include_once('Exceptions/variables.php');

class HuError_settings extends settings{
#Defaults
protected $__SETS = array(
	/**
	* @example HuLOG.php
	*/
	'FORMAT_WEB'		=> array(),	/** For strToWeb().		If empty (by default): dump::w */
	'FORMAT_CONSOLE'	=> array(),	/** For strToConsole().	If empty (by default): dump::c */
	'FORMAT_FILE'		=> array(),	/** For strToFile().	If empty (by default): dump::log */

	/**
	* @see ::updateDate()
	**/
	'AUTO_DATE'		=> true,
	'DATE_FORMAT'		=> 'Y-m-d H:i:s',
);

/**
* @example
* protected $__SETS = array(
*	#В формате settings::getString(array)
*	'FORMAT_CONSOLE'	=> array(
*			array('date', "\033[36m", "\033[0m"),
*		'level',
*			array('type', "\033[1m", "\033[0m: ", ''),//Bold
*		'logText',
*			array('extra', "\n"),
*		"\n"
*		),
*	'FORMAT_WEB'	=> array(
*			array('date', "<b>", "</b>"),
*		'level',
*			array('type', "<b>", "</b>: ", ''),
*		'logText',
*			array('extra', "<br\\>\n"),
*		"<br\\>\n"
*		),
*	'FORMAT_FILE'	=> array(
*		'date',
*		'level',
*			array('type', '', ': ', ''),
*		'logText',
*			array('extra', "\n"),
*		"\n"
*		),
*	),
*	);
**/
}#c HuError_settings

class HuError extends settings{
/** Self settings. **/
protected /* settings */ $_sets = null;
protected $_curTypeOut = OS::OUT_TYPE_BROWSER;

	public function __construct(HuError_settings $sets = null){
	$this->_sets = EMPTY_VAR($sets, new HuError_settings);
	}#m __construct

	/**
	* Due to absent mutiple inheritance in PHP, just copy/pasted from class get_settings.
	* Переопределяем, чтобы сделать ссылку на настройки не изменяемой!
	* таким образом настройки менять можно будет, а сменить объект настроек - нет
	* @param string Needed name
	* @return mixed Object of settings.
	**/
	function &__get ($name){
		switch ($name){
		case 'settings': return $this->_sets;
		break;
		case 'date':
		case 'DATE':
			if (!@$this->getProperty($name)) $this->updateDate();
		//break;	/** NOT need break. Create by read, and continue return value!

		default:
		/**
		* Set properties is implicit and NOT returned reference by default.
		* But for 'settings' we want opposite reference. Whithout capability of functions
		* overload by type arguments - is only way silently ignore Notice: Only variable references should be returned by reference
		**/
		$t = $this->getProperty($name);
		return $t;
		}
	}#__get

	/**
	* String to print into file.
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_FILE setting used.
	*	And if it settings empty (or not exists) too, just using dump::log() for all filled fields.
	* @return string
	**/
	public function strToFile($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_FILE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_FILE)) return $this->getString($format);
		else return dump::log($this->__SETS, null, true);
	}#m strToFile

	/**
	* String to print into user browser.
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_WEB setting used.
	*	And if it settings empty (or not exists) too, just using dump::w() for all filled fields.
	* @return string
	**/
	public function strToWeb($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_BROWSER;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_WEB)) return $this->getString($format);
		else return dump::w($this->__SETS, null, true);
	}#m strToWeb

	/**
	* String to print into user brawser.
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_CONSOLE setting used.
	*	And if it settings empty (or not exists) too, just using dump::c() for all filled fields.
	* @return string
	**/
	public function strToConsole($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_CONSOLE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_CONSOLE)) return $this->getString($format);
		else return dump::c($this->__SETS, null, true);
	}#m strToConsole

	/**
	* String to print. Automaticaly detect Web or Console. Detect by {@link OS::getOutType()}
	*	and invoke appropriate ::strToWeb() or ::strToConsole()
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = ''){
	$this->_curTypeOut = OS::OUT_TYPE_PRINT;//Pseudo. Will be clarified.
		if (OS::OUT_TYPE_BROWSER == OS::getOutType()) return $this->strToWeb($format);
		else return $this->strToConsole($format, null, true);
	}#m strToPrint

	/**
	* Convert to string by type.
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = ''){
	$this->_curTypeOut = $type;
		switch ($type){
		case OS::OUT_TYPE_BROWSER:
		return $this->strToWeb($format);
		break;

		case OS::OUT_TYPE_CONSOLE:
		return $this->strToConsole($format);
		break;

		case OS::OUT_TYPE_FILE:
		return $this->strToFile($format);
		break;

		#Addition
		case OS::OUT_TYPE_PRINT:
		return $this->strToPrint($format);
		break;

		default:
		throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m strByOutType

	/**
	* On echo and print was detect, and provide correct form
	* @return string ::strToPrint()
	**/
	public function __toString(){
	return $this->strToPrint();
	}#m __toString

	/**
	* Overload settings::setSetting() to handle autodate
	* @inheritdoc
	**/
	public function setSetting($name, $value){
	parent::setSetting($name, $value);

	$this->updateDate();
	}#m setSetting

	/**
	* Overload settings::setSettingsArray() to handle autodate
	* @inheritdoc
	* @return $this
	**/
	public function setSettingsArray(array $setArr){
	parent::setSettingsArray($setArr);

	#Insert after update data
	$this->updateDate();
	return $this;
	}#m setSettingsArray

	/**
	* Just alias for ::setSettingsArray()
	* @param	$setArr
	* @return mixed	::setSettingsArray()
	**/
	public function setFromArray(array $setArr){
	return $this->setSettingsArray($setArr);
	}#m setFromArray

	/**
	* Overload settings::mergeSettingsArray() to handle autodate
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
	#Insert BEFORE update data in merge. User data 'date' must overwrite auto, if present!
	$this->updateDate();

	parent::mergeSettingsArray($setArr);
	}#m mergeSettingsArray

	/**
	* Just alias for ::mergeSettingsArray()
	* @param	$setArr
	* @return mixed	::mergeSettingsArray()
	**/
	public function mergeFromArray(array $setArr){
	$this->mergeSettingsArray($setArr);
	}#m mergeFromArray

	/** If settings->AUTO_DATE == true and settings->DATE_FORMAT correctly provided - update current
	* date on ->date
	* @param
	* @return
	**/
	public function updateDate(){
		if (
			$this->settings->AUTO_DATE
			and
			/** Parent::setSetting instead $this-> to aviod infinity recursion */
			$this->settings->DATE_FORMAT) parent::setSetting('date', date($this->settings->DATE_FORMAT));
	}#m updateDate

	/**
	* Overloading getString to separetly handle 'extra'
	* @inheritdocs	
	**/
	public function formatField($field){
		if (is_array($field)){
			 if(!isset($field[0])) $field = array_values($field);
		$fieldValue = @$this->{$field[0]};
		}
		else{ 
		$field = (array)$field;
		$fieldValue = EMPTY_VAR(@$this->{$field[0]}, $field[0]); //Setting by name, or it is just text
		}

		if ($fieldValue instanceof HuError){
		return NON_EMPTY_STR($fieldValue->strByOutType($this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		elseif($fieldValue instanceof backtrace){
		return NON_EMPTY_STR($fieldValue->printout(true, null, $this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		else return NON_EMPTY_STR($fieldValue, @$field[1], @$field[2], @$field[3]);
	}#m formatField
}#c HuError

/**
* To allow out any data
**/
class ExtraData extends HuError{
	/**
	* Constructor
	* @param mixed	$data
	**/
	public function __construct($data){
	$this->__SETS = $data;
	}#__c
}#c ExtraData
?>