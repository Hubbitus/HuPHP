<?php
declare(strict_types=1);

/**
* In PHP we unfortunately do not have multiple inheritance :(
*
* Common implementation will be present in comments near after declaration.
**/

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;

interface IOutExtraData {
	//public $_curTypeOut = OS::OUT_TYPE_BROWSER; //Track to helpers, who provide format (parts) and need known for what

	/**
	* String to print into file. Primary for logs string representation
	*
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToFile($format = null);

	/**
	* Return string to print into user browser.
	*
	* @param * @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToWeb($format = null);

	/**
	* String to print on console.
	*
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	**/
	public function strToConsole($format = null);

	/**
	* String to print. automatically detect (by {@link OS::getOutType()}) Web or Console and
	*	invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param string $format	If @format not-empty use it for formatting result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = null);

	/**
	* Convert to string by provided type.
	*
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = null);
}
