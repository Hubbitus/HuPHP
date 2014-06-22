<?
/**
* In PHP we unfortunately do not have multiple inheritance :(
* So, turn it class into interface and provide common, possible implementation
* through static methods of __outExtraData__common_implementation homonymous methods
* and providing link to $this and in method implementation refer to it as &obj instead of direct $this.
*
* Common implementation will be present in comments near after declaration.
**/

interface outExtraData{
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
	* String to print. Automaticaly detect (by {@link OS::getOutType()}) Web or Console and
	*	invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param string $format	If @format not-empty use it for formating result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strToPrint($format = null);/*{Now common solution is (see description on begin abput Multiple Inheritance):
	return __outExtraData__common_implementation::strToPrint($this, $format);
	}#m strToPrint
	*/

	/**
	* Convert to string by provided type.
	*
	* @param integer $type	One of OS::OUT_TYPE_* constant. {@link OS::OUT_TYPE_BROWSER}
	* @param mixed(null)	$format Any useful helper information to format
	* @return string
	* @Throw(VariableRangeException)
	**/
	public function strByOutType($type, $format = null);/*{Now common solution is (see description on begin abput Multiple Inheritance):
	return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	*/
}#c

/* see description on begin about Multiple Inheritance **/
class __outExtraData__common_implementation{
	//Only hack - common realization!
	public static function strByOutType(/*$this*/&$obj, $type, $format = null){
		$obj->_curTypeOut = $type;

		switch ($type){
			case OS::OUT_TYPE_BROWSER:
			return $obj->strToWeb($format);
				break;

			case OS::OUT_TYPE_CONSOLE:
				return $obj->strToConsole($format);
				break;

			case OS::OUT_TYPE_FILE:
				return $obj->strToFile($format);
				break;

			// Addition, pseudo
			case OS::OUT_TYPE_PRINT:
				return $obj->strToPrint($format);
				break;

			default:
				throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m strByOutType

	public function strToPrint(/*$this*/&$obj, $format = null){
		$obj->_curTypeOut = OS::OUT_TYPE_PRINT;//Pseudo. Will be clarified.
		if (OS::OUT_TYPE_BROWSER == OS::getOutType()) return $obj->strToWeb($format);
		else return $obj->strToConsole($format);
	}#m strToPrint
}#c
?>