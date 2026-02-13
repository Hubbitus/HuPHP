<?php
declare(strict_types=1);

/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.1.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2008-05-31 03:19
*
* @uses EMPTY_VAR()
* @uses NON_EMPTY_STR()
* @uses settings
* @uses debug
* @uses OS
* @uses VariableRangeException
* @uses outExtraData.interface
**/

include_once('macroses/EMPTY_VAR.php');

class HuErrorSettings extends Settings{
	// Defaults
	protected $__SETS = array(
		/**
		* @example HuLOG.php
		**/
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
	*	//В формате settings::getString(array)
	*	'FORMAT_CONSOLE'	=> array(
	*		array('date', "\033[36m", "\033[0m"),
	*		'level',
	*		array('type', "\033[1m", "\033[0m: ", ''),//Bold
	*		'logText',
	*		array('extra', "\n"),
	*		"\n"
	*	),
	*	'FORMAT_WEB'	=> array(
	*		array('date', "<b>", "</b>"),
	*		'level',
	*		array('type', "<b>", "</b>: ", ''),
	*		'logText',
	*		array('extra', "<br\\>\n"),
	*		"<br\\>\n"
	*	),
	*	'FORMAT_FILE'	=> array(
	*		'date',
	*		'level',
	*		array('type', '', ': ', ''),
	*		'logText',
	*		array('extra', "\n"),
	*		"\n"
	*		),
	*	),
	* );
	**/
}
class HuError extends Settings implements OutExtraData {
	/** Self settings. **/
	protected /* settings */ $_sets = null;
	public $_curTypeOut = OS::OUT_TYPE_BROWSER; //Track to helpers, who provide format (parts) and need known for what

	public function __construct(?HuErrorSettings $sets = null){
		$this->_sets = EMPTY_VAR($sets, new HuErrorSettings);
	}
	/**
	* Due to absent multiple inheritance in PHP, just copy/paste from class {@see SettingsGet} @TODO switch to use traits.
	* Overloading to provide ref on settings without change possibility.
	* In this case change settings is allowed, but change full settings object - not!
	*
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
			* But for 'settings' we want opposite reference. Without compatibility of functions
			* overload by type arguments - is only way silently ignore Notice: Only variable references should be returned by reference
			**/
			$t = $this->getProperty($name);
			return $t;
		}
	}
	/**
	* String to print into file.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_FILE setting used.
	*	And if it settings empty (or not exists) too, just using dump::log() for all filled fields.
	* @return string
	**/
	public function strToFile($format = null){
		$this->_curTypeOut = OS::OUT_TYPE_FILE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_FILE)) return $this->getString($format);
		else return Dump::log($this->__SETS, null, true);
	}
	/**
	* String to print into user browser.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_WEB setting used.
	*	And if it settings empty (or not exists) too, just using dump::w() for all filled fields.
	* @return string
	**/
	public function strToWeb($format = null){
		$this->_curTypeOut = OS::OUT_TYPE_BROWSER;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_WEB)) return $this->getString($format);
		else return Dump::w($this->__SETS, null, true);
	}
	/**
	* String to print on console.
	*
	* @param string $format If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. If empty string, FORMAT_CONSOLE setting used.
	*	And if it settings empty (or not exists) too, just using dump::c() for all filled fields.
	* @return string
	**/
	public function strToConsole($format = null){
		$this->_curTypeOut = OS::OUT_TYPE_CONSOLE;
		if ($format = EMPTY_VAR($format, @$this->settings->FORMAT_CONSOLE)) return $this->getString($format);
		else return Dump::c($this->__SETS, null, true);
	}
	/**
	* String to print. automatically detect Web or Console. Detect by {@link OS::getOutType()}
	*	and invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = null){
		return __outExtraData__common_implementation::strToPrint($this, $format);
	}
	/**
	* Convert to string by type.
	*
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = null){
		return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	}
	/**
	* Detect appropriate print (to Web or Console) and return correct form
	*
	* @return string ::strToPrint()
	**/
	public function __toString(){
		return $this->strToPrint();
	}
	/**
	* Overload settings::setSetting() to handle autodate
	*
	* @inheritdoc
	**/
	public function &setSetting($name, $value){
		parent::setSetting($name, $value);

		$this->updateDate();
	}
	/**
	* Overload settings::setSettingsArray() to handle autodate
	*
	* @inheritdoc
	* @return $this
	**/
	public function setSettingsArray(array $setArr){
		parent::setSettingsArray($setArr);

		//Insert after update data
		$this->updateDate();
		return $this;
	}
	/**
	* Just alias for ::setSettingsArray()
	*
	* @param	$setArr
	* @return mixed	::setSettingsArray()
	**/
	public function setFromArray(array $setArr){
		return $this->setSettingsArray($setArr);
	}
	/**
	* Overload settings::mergeSettingsArray() to handle autodate
	*
	* @inheritdoc
	**/
	public function mergeSettingsArray(array $setArr){
		//Insert BEFORE update data in merge. User data 'date' must overwrite auto, if present!
		$this->updateDate();

		parent::mergeSettingsArray($setArr);
	}
	/**
	* Just alias for ::mergeSettingsArray()
	*
	* @param	$setArr
	* @return mixed	::mergeSettingsArray()
	**/
	public function mergeFromArray(array $setArr){
		$this->mergeSettingsArray($setArr);
	}
	/**
	* If settings->AUTO_DATE == true and settings->DATE_FORMAT correctly provided - update current
	* date in ->date
	*
	* @return
	**/
	public function updateDate(){
		if (
			$this->settings->AUTO_DATE
			and
			/** Parent::setSetting instead $this-> to aviod infinity recursion */
			$this->settings->DATE_FORMAT
		)
			parent::setSetting('date', date($this->settings->DATE_FORMAT));
	}
	/**
	* Overloading getString to separetly handle 'extra'
	*
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

		if ($fieldValue instanceof OutExtraData){
			return NON_EMPTY_STR(@$fieldValue->strByOutType($this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		elseif($fieldValue instanceof Backtrace){
			return NON_EMPTY_STR(@$fieldValue->printout(true, null, $this->_curTypeOut), @$field[1], @$field[2], @$field[3]);
		}
		else return NON_EMPTY_STR($fieldValue, @$field[1], @$field[2], @$field[3]);
	}
}
