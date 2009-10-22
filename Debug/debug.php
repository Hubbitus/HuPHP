<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage Debug
* @version 2.4.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-29 15:58 Version 2.3 from 2.2.b
*	- Add config-parameter "display_errors", default true.
*	- Move methods transformCorrect_print_r and transformCorrect_var_dump to separate class dump_utils (dump_utils.php)
*	- Move dump::log into log_dump.php in separate function log_dump.
*		dump::log ReRealise with it.
*		It is isfull fo not only debug purpose, and very bad what it depends from much debug-tools (classes, functions, files)
*
*	* 2008-06-06 16:40 Ver 2.3 to 2.3.1
*	- Include Debug/log_dump.php (in dump::log) and realize dump::log through log_dump free function.
*	- Delete all deprecated free functions!
*
*	* 2008-08-27 19:15 Ver 2.3.1 to 2.3.2
*	- Handle xdebug.overload_var_dump option in dump::w
*
*	* 2008-09-15 22:15 Ver 2.3.2 to 2.3.3
*	- Prevent html-output in dump::c even if html_errors=On
*
*	* 2008-10-04 22:25 ver 2.3.3 to 2.3.4
*	- Add bacward-capability function implementation of function spl_object_hash() if it is not exists.
*
*	* 2009-01-30 15:10 ver 2.3.4 to 2.3.5
*	- Add include_once('macroses/ISSET_VAR.php');
*	- All checks to $__CONFIG values replaced by call call to macros {@see ISSET_VAR}.
*		Full explanation reason of it see in description of macros {@see ISSET_VAR}
*
*	* 2009-06-16 13:11 ver 2.3.5 to 2.4
*	- Separate set $GLOBALS['__CONFIG']['debug'][*] options default values.
*	- Remove unused $GLOBALS['__CONFIG']['debug']['whithout_Tokenizer'] option.
*	- ISSET_VAR() check replaced by array_key_exists() (see internal comment below).
*	- As now all $GLOBALS['__CONFIG']['debug'][*] settings all has default values, we may direct check its. It faster and reliable.
*		So, exclude all ISSET_VAR call and inclusion such macros.
*	- Remove define ('NO_DEBUG', false); this constant was unused.
*
*	* 2009-06-18 15:21 ver 2.4 to 2.4.1
*	- Inicialize $GLOBALS['__CONFIG']['debug'] as empty array if it is not defined yet to avoid futher warnings
*
*	* 2009-10-22 16:20 ver 2.4.1 to 2.4.2
*	- Add method ::e()
**/

define ('DUMP_DO_NOT_DEFINE_STUMP_DUMP', true);
include_once('Debug/dump_utils.php');

	//Avoid warning
	if (!array_key_exists('__CONFIG', $GLOBALS) or !array_key_exists('debug', $GLOBALS['__CONFIG'])) $GLOBALS['__CONFIG']['debug'] = array();

	/**
	* @internal
	* Even here, used directly $GLOBALS, because it may be included in other scope (e.g. from function)
	*
	* We MUST use direct array_key_exists instead if isset (or even is_set) check because, according
	*	to MAN: "isset() will return FALSE if testing a variable that has been set to NULL", but we really want known fact of presence.
	**/
	if (!array_key_exists('parseCallParam', $GLOBALS['__CONFIG']['debug'])){
	/**
	* Parsing what parameters present at call time.
	* For example:
	* dump::c($ttt)
	* is equivalent to
	* dump::c($ttt, '$ttt')
	* This future is very usefull, but require Tokenizer class and got time overhead.
	* @global	integer	$GLOBALS['__CONFIG']['debug']['parseCallParam']
	**/
	$GLOBALS['__CONFIG']['debug']['parseCallParam'] = true;
	}

	if (!array_key_exists('errorReporting', $GLOBALS['__CONFIG']['debug'])){
	/**
	* Set error_reporting to this value.
	* Null has special means - no change!
	* @global	integer	$GLOBALS['__CONFIG']['debug']['errorReporting']
	**/
	$GLOBALS['__CONFIG']['debug']['errorReporting'] = E_ALL;
	}
	if (null !== $GLOBALS['__CONFIG']['debug']['errorReporting']){
	error_reporting($GLOBALS['__CONFIG']['debug']['errorReporting']);
	}

	if (!array_key_exists('display_errors', $GLOBALS['__CONFIG']['debug'])){
	/**
	* Enable or disable global errors reporting.
	* @global	integer	$GLOBALS['__CONFIG']['debug']['display_errors']
	**/
	$GLOBALS['__CONFIG']['debug']['display_errors'] = 1;
	}
	if (null !== $GLOBALS['__CONFIG']['debug']['display_errors']){
	ini_set('display_errors', $GLOBALS['__CONFIG']['debug']['display_errors']);
	}

	if ($GLOBALS['__CONFIG']['debug']['parseCallParam']){
	include_once('Debug/Tokenizer.php');
	include_once('Debug/backtrace.php');
	}

/**
* @package Debug
* Mainly for emulate namespace
* Most (all?) methods are static
**/
class dump extends dump_utils{
	/**
	* Return $header. If in $header present - return as is, else make guess as real be invoked.
	*
	* @param &mixed $header. Be careful! By default, in parent methods like dump::*() $header=false!
	*	If passed $header === null it allows distinguish what it is not passed by default or
	*	it is not needed!!
	* @return &mixed $var
	**/
	static public function getHeader(&$header, &$var){
		if ($header){
		return $header;
		}
		elseif(
			//Be careful! Null, NOT false by default in dump::*()! It allows distinguish what it is
			//not passed by default or it is not needed!!
			$header !== null
			and $GLOBALS['__CONFIG']['debug']['parseCallParam']
			and
			(
				$cp = Tokenizer::trimQuotes(
					Tokenizer::create(
						backtrace::create()->find(
							backtraceNode::create(
								array(
									'class'	=> 'dump',
									'function'=> '[awce]',
									'type'	=> '::'
								)
							)
						)->end()
					)->parseCallArgs()->getArg(0)
				)
			)
			!= ( is_object($var) ? spl_object_hash($var) : (string)$var ) /* PHP Catchable fatal error NOT handled traditionaly
			with try-catch block!
			See http://ru2.php.net/manual/en/migration52.error-messages.php
			and http://www.zend.com/forums/index.php?t=rview&th=2607&goto=6920
			*/
			) return $cp;
//		else return 'Unknown';
	}#m getHeader

	/**
	* Console dump. Useful in cli-php. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function c($var, $header = false, $return = false){
	$ret = '';

		if ($header = self::getHeader($header, $var)) $ret .= "\033[1m".$header."\033[0m: ";

	ob_start();
		//This may happens. F.e. it present in template class
		if ($return_html_errors = ini_get('html_errors')){
		ini_set('html_errors', false);
		}
	var_dump($var);//This isn't possible return string in other way, such as it possible in print_r(, true)
	$dStr = ob_get_clean();
	$ret .= self::transformCorrect_var_dump($dStr)."\n";

		if ($return_html_errors) //Revertb back
		ini_set('html_errors', true);

		if ($return) return $ret;
		else echo $ret;
	}#m c

	/**
	* Log dump. Useful to return string for file-write. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function log($var, $header = false, $return = true){
	include_once('Debug/log_dump.php');
	return log_dump($var, $header, $return);
	}#m log

	/**
	* Buffered dump. Useful to return string for file-write. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param string|array	Callback-function or array(object, 'method')
	* @return string|void	Depend of parameter $return
	**/
	static public function buff($var, $header = false, $debug_func = 'print_r'){
	/*
	* For use with family ob_*!
	* In this case do not restricted use standart print_r, var_dump and var_export
	*
	* Out to stderr, instead of stdout
	* This is "no good" method, but it is worked for me.
	* $extra may contain only SHORT aliases!
	*/
	$header = self::getHeader($header, $var);

	$print_func = 'include_once("Debug/debug.php"); '.$debug_func;
	$cmd = 'echo "<? '.$print_func.'(unserialize('.addcslashes(escapeshellarg(serialize($var)),'"').')'.($extra ? ",'".addcslashes($header, '$')."'" : '').');?>" | php';
	file_put_contents('php://stderr', shell_exec($cmd));
	}#m buff

	/**
	* Short alias to Buffered Console Dump. Parameters are same. See appropriate methods
	**/
	static public function b_c($var, $header = false){
	$header = self::getHeader($header, $var);

	return dump::buff($var, $header, 'dump::c');
	}#m b_c

	/**
	* WEB dump. Useful to dump in Web-browser. See also {@link ::a()} and {@link ::auto()}
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	*	$header = ::getHeader($header, $var) . See {@link ::detHeader()} for more details and
	*	distinguish false and null values handle.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function w($var, $header = false, $return = false){
	$ret = '';
		if ($header = self::getHeader($header, $var)) $ret .= '<h4 style="color:green">'.$header.":</h4>\n";

	ob_start();
	var_dump($var);//This isn't possible return string in other way, such as it possible in print_r(, true)
	$dStr = ob_get_clean();

		#if (ini_get('xdebug.overload_var_dump')){
		# Config-directives not always is set...
		if ('<pre' == substr($dStr, 0, 4)){
		$ret .= $dStr;
		}
		else{//By hand
		$ret .= '<pre><xmp>';
		// $ret .= self::transformCorrect_print_r(print_r($var, true))."\n";
		$ret .= self::transformCorrect_var_dump($dStr)."\n";
		$ret .= '</xmp></pre>';
		}

		if ($return) return $ret;
		else echo $ret;
	}#m w

	/**
	* WAP dump. Useful to dump in WAP-browser (XML).
	*
	* @param	mixed $var Variable (or scalar) to dump.
	* @param string|false	$header. Header to prepend dump of $var.
	* @param boolean $return If true - return result as string instead of echoing.
	* @return string|void	Depend of parameter $return
	**/
	static public function wap($var, $header = false, $return = false){
	$ret = '';
		if ($header) $ret .= '<h4>'.$header."</h4>\n";	#Only explicitly given
	$ret .= nl2br(print_r($var, true)).'<br />';
		if ($return) return $ret;
		else echo $ret;
	}#m wap

	/**
	* Make guess how invoked from cli or from WEB-server (any other) and turn next to c_dump or w_dump respectively.
	*
	* @return mixed	::c or ::w invoke whith same parameters.
	**/
	static public function auto($var, $header = false, $return = false){
		/**
		* May use php_sapi_name() or (in notice of this) constant PHP_SAPI. Use second.
		*/
		if (PHP_SAPI == 'cli') return self::c($var, $header, $return);
		else return self::w($var, $header, $return);
	}#m auto

	/**
	* Only short alias for {@link ::auto()}, nothing more!
	*
	* @return mixed	::c() or ::w() invoke whith same parameters.
	**/
	static public function a($var, $header = false, $return = false){
	return self::auto($var, $header, $return);
	}#m a

	/**
	* As {@link ::a()} but print to STDERR!
	*
	* @return	nothing
	**/
	static public function e($var, $header = false){
	file_put_contents('php://stderr', self::auto($var, $header, true));
	}#m e

	/**
	* One name to invoke dependently by out type.
	*
	* @return mixed One of result call: ::c, ::a, ::log, ::wap.
	* @Throw(VariableRangeException)
	**/
	public static function byOutType($type, $var, $header = false, $return = false){
	include_once('System/OS.php');
		switch ($type){
		case OS::OUT_TYPE_BROWSER:
		return self::w($var, $header, $return);
		break;

		case OS::OUT_TYPE_CONSOLE:
		return self::c($var, $header, $return);
		break;

		case OS::OUT_TYPE_FILE:
		return self::log($var, $header, $return);
		break;

		case OS::OUT_TYPE_WAP:
		return self::wap($var, $header, $return);
		break;

		#Addition
		case OS::OUT_TYPE_PRINT:
		return self::a($var, $header, $return);
		break;

		default:
		include_once('Exceptions/variables.php');
		throw new VariableRangeException('$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!');
		}
	}#m byOutType
}#c debug

/**
* dump::getHeader assumed on spl_object_hash() for objects, so, we must emulate it on old versions of PHP.
* Simple implementation got from http://xpoint.ru/forums/programming/PHP/thread/38733.xhtml
*
* @param Object $obj
* @return string - object hash.
**/
if (!function_exists("spl_object_hash")) {
	function spl_object_hash($obj){
	static $cur_id = 0;
		if (!is_object($obj))
		return null;

		!isset($obj->_obj_id_) and $obj->_obj_id_ = md5($cur_id++);

	return $obj->_obj_id_;
	}
}
?>