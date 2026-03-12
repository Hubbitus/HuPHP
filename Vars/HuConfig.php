<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\Vars\Settings\SettingsCheck;

/**
* Class to provide easy access to $GLOBALS['__CONFIG'] variables.
* Intended use with Singleton class as:
* @example Single::def('HuConfig')->config_value
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-01 22:12
**/
class HuConfig extends SettingsCheck {
	private $_include_tried = [];

	public function __construct() {
		parent::__construct(\array_keys($GLOBALS['__CONFIG']), $GLOBALS['__CONFIG']);
	}

	/**
	* As __get before.
	* Now {@see __get()} reimplemented to return HuArray instead of raw arrays
	* Bee careful - after standard call (not raw) original Array value was replaced by HuArray!
	*
	* @param string $varname
	* @param bool $noThrow If true - silently not thrown any exception.
	**/
	public function &getRaw($varname, $noThrow = false): mixed {
		return $this->getProperty($varname, $noThrow);
	}

	/**
	* For more comfort access in config fields without temporary variables like:
	* Single::def('HuConfig')->test->first
	*
	* @param string $varname
	**/
	public function &__get($varname): mixed {
		$ret =& $this->getProperty($varname);

		if (\is_array($ret)) {
			$ret = new HuArray($ret); //Replace original on the fly
			return $ret;
		}
		else {
			return $ret;
		}
	}

	/**
	* Reimplement as initial, only return value by reference
	* Also try include file 'includes/configs/' . $name . '.config.php' if it exist to find needed settings.
	* @inheritdoc
	* @param string	$name
	* @param bool $noThrow=false If true - silently not thrown any exception.
	**/
	public function &getProperty($name, $noThrow = false): mixed {
		try {
			return $this->__SETS[$this->checkNamePossible(Vars::requiredNotNull($name), __METHOD__)];
		}
		catch(\Throwable $e) {
			//Try include appropriate file only for ClassPropertyNotExistsException:
			if ($e instanceof ClassPropertyNotExistsException && !\in_array($name, $this->_include_tried, true)) {
				$this->_include_tried[] = $name; //In any case to do not check again next time
				$path = 'includes/configs/' . $name . '.config.php';
				if(OS::is_includeable($path)) {
					include($path);
				}
				if(isset($GLOBALS['__CONFIG'][$name])) { //New key
					$this->addSetting($name, $GLOBALS['__CONFIG'][$name]);
				}
				return $this->__SETS[$name];
			}

			//Silent if required.
			if (!$noThrow) {
				throw $e; //If include and fine failed throw outside;
			}
			else {
				// Avoid: Notice: Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 101
				$t = null;
				return $t;
			}
		}
	}
}
