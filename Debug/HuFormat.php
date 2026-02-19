<?php
declare(strict_types=1);

/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuFormat
* @version 2.1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created  2009-03-13 19:01
*
* @uses EMPTY_STR()
* @uses ASSIGN_IF()
* @uses REQUIRED_VAR()
*
* @uses VariableException
* @uses VariableRangeException
* @uses VariableRequiredException
*
* @uses HuError
* @uses Single
* @uses OS
**/

namespace Hubbitus\HuPHP\Debug;

use function Hubbitus\HuPHP\Macroses\NON_EMPTY_STR;
use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassMethodException;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;

class HuFormatException extends VariableException {}

/**
* Class to format different structures.
* @example HuFormat.example.php
**/
class HuFormat extends HuError {
	/** Replace this in ->_format on real value of _value (after process mod_s) **/
	public const string sprintf_var = '__vAr__';
	/** Var to process in eval-string in mod_e. In eval string off course witch sign $. **/
	public const string evaluate_var = 'var';

	/** Separator to separate mods from name in one string. For more info see {@see ::parseModsName()} **/
	public const string mods_separator = ':::';

	/**
	* For each present Mod we must have method with name "mod_[mod]" where [mod] is letter of mode.
	*	Additionally, because PHP function (methods too) name are case insensitive, for upper-case letter
	*	modifiers must used double letters.
	*	For example:
	*		Mod 'e' => mod_e
	*		Mod 'E' => mod_EE (same as mod_ee)
	*
	* @var array
	**/
	public static array $MODS = [
		'A'	=> 1,	//ALL. Exclusive, all other modifiers not processed. Each process as HuFormat.
		's'	=> 2,	//Setting
		'a'	=> 4,	//Array
		'n'	=> 8,	//Non_empty_str
		'p'	=> 16,	//sPrintf. {@link http://php.net/sprintf}
		'e'	=> 32,	//Evaluate. Evaluated only ->_name !!!
		'E'	=> 64,	//Evaluate full! Evaluate all as full result.
		'v'	=> 128,	//Value,
		'I'	=> 256,	//Iterate ->_value (or ->_realValue) and each format as ->_format
		'k'	=> 512,	//Key. Get key of current iteration of I:::.
	];

	private $_format;				//Array of format.
	private $_modStr;				//Modifiers.
	private $_mod;					//Integer of present mods
	private $_modArr = [];			//Array of present mods
	private $_value;				//Value, what processed in this formatting.
	private $_realValue;			//If modified (part) in mod_s, mod_a
	private $_realValued = false;	//Flag, to allow pipe through several mods (like as s. a, e)
	private $_name;
	private $_key;					//Key from mod_I iteration for the mod_k

	private $_resStr;				//For caching

	/**
	* @method Object(settings) sets() return current settings
	**/

	/**
	* @method Object(HuFormat) create() Return new instance of object.
	**/

	/**
	* Constructor
	*
	* {@see ::set()}
	*	Be careful - you should explicit provide value like false (invoke as __construct(null, $t = false) for example, because 2d parameter is reference). Otherwise default value null means - using $this as value!
	**/
	public function __construct(array $format = null, &$value = null, $key = null){
		$this->set($format, $value, $key);
	}

	/**
	* Set main: format and value.
	*
	* @param	array|string	$format. If === null, skipped to allow set other
	*	parts. To clear you may use false/true or any else such as empty string.
	* @param	&mixed	$value.	{@see ::setValue()} Skipped if === null. You
	*	may call {@see ::setValue()} to do that
	* @param	mixed	$key	Key of iteration in mod_I and/or mod_A.
	* @return	&$this
	**/
	public function &set($format = null, &$value = null, $key = null){
		if (null !== $value) $this->setValue($value);
		if (null !== $format) $this->setFormat($format);
		$this->_key = $key;
		return $this;
	}

	/**
	* Return current value.
	*
	* @return &mixed
	**/
	public function &getValue(): mixed {
		if ($this->_realValued) return $this->_realValue;
		else return $this->_value;
	}

	/**
	* Set value
	*
	* @param	&mixed	$value.	Value to format.
	*	If === null $this->_value =& $this; $this->_realValue =& $this->_value;
	* @return &$this
	**/
	public function &setValue(&$value): static {
		if(null === $value){
		$this->_value =& $this;
		}
		else $this->_value = $value;
		$this->_realValued = false;
		$this->_resStr = null;
		return $this;
	}

	/**
	* Parse incoming format-array and set appropriate properties.
	* Accepts 3 forms of format (in examples ':::' is {@see ::parseModsName()}):
	*	1. All in one Array: [0] - Mods:::Name; [1],[2],[3]...[n] - data.
	*	array(
	*		'sn:::bold_text',	//Mods:::Name
	*		'<b>', '</b>', 'default text'	//Data
	*	)
	*
	*	2. Associative array (hash): Key - Mods:::Name, value - Array of [0] - Mods, [1], [2]...[n] - data.
	*	array(
	*		//Name
	*		'bold_text'	=> array(	//Mods empty, Name
	*			'<b>', '</b>', 'default text'	//Data
	*		)
	*	)
	*
	*	3. Just simply string like 'text to add'. Leaved as is.
	*
	* @param array|string	$format to parse
	* @return &$this
	**/
	public function &setFormat($format): static {
		$this->_mod = 0;
		$this->_modStr = $this->_name = $this->_resStr = $this->_realValue = null;
		$this->_modArr = array();
		$this->_realValued = false;

		if (is_array($format)){
			if (is_array($format[key($format)])){//<2>
				$this->parseModsName(key($format));
				$this->_format = $format[key($format)];
			}
			else{//<1>
				$this->parseModsName(array_shift($format));
				$this->_format = $format;//Tail
			}
		}
		else{//<3>
			$this->_name = $this->_realValue = $format;
			$this->_realValued = true;
		}

		return $this;
	}

	/**
	* Parses and set from given str. As separator used {@see self::mods_separator}.
	* F.e.: 'AI:::line'. If separator not present - whole string in NAME!
	*
	* @param string $str
	* @return &$this
	**/
	protected function &parseModsName($str): HuFormat {
		if (!strstr($str, self::mods_separator)){//Whole name
			$this->_name = $str;
			$this->_modStr = '';
		}
		else{//Separator present
			list ($this->_modStr, $this->_name) = explode(self::mods_separator, $str);
		}
		return $this->parseMods(true);
	}

	/**
	* Construct and return string to represent provided value according given format.
	*
	* @param array|null $fields Fields to include in the string representation (for compatibility with parent class)
	* @return string
	**/
	public function getString(?array $fields = null): string {
		if (!$this->_resStr){
			$this->_resStr = '';

			foreach ($this->_modArr as $mod){
				if (\ctype_upper($mod)){
					$result = \call_user_func([$this, 'mod_'.$mod.$mod]);
					if ($result !== null) {
						$this->_resStr .= $result;
					}
				}
				else {
					$result = \call_user_func([$this, 'mod_'.$mod]);
					if ($result !== null) {
						$this->_resStr .= $result;
					}
				}
			}

			//If all mod_* are only evaluate value and not produce out.
			if (!$this->_resStr) {
				$value = $this->getValue();
				return $value !== null ? (string)$value : '';
			}
		}

		return $this->_resStr;
	}

	/**
	* Set or not?
	*
	* @param integer	$mod.
	* @return bool
	**/
	public function isMod($mod): bool {
		if (!$this->_mod and $this->_modstr) $this->parseMods();
		return (bool)($this->_mod & $mod);
	}

	/**
	* Set, or unset mods.
	*
	* @param string	$mods. String to set o unset Mods like: '-I+s+n'.
	*	If '-' - unset.
	*	If '+' - set.
	*	If '*' - invert.
	*	If absent - equal to '+'
	* @return &$this
	* @throws VariableRangeException
	**/
	public function &changeModsStr($mods): static {
		for($i=0; $i < strlen($mods); $i++){
			if (in_array($mods[$i], array('+', '-', '*'))){
				$op = $mods[$i];
				$mod = $mods[++$i];
			}
			else{
				$mod = $mods[$i];
				$op = '+';	//Default
			}

			switch ($op){
				case '+':
					$this->_mod |= self::$MODS[$mod];
					break;

				case '-':
					$this->_mod ^= self::$MODS[$mod];
					break;

				case '*':
					$this->_mod &= ~self::$MODS[$mod];
					break;

				default:
					throw new VariableRangeException('Unknown operator - "'.$op.'"');
			}
		}

		$this->parseMods(false);
		return $this;
	}

	/**
	* Set Modifiers from string.
	*
	* @param string	$modStr	String of modifiers.
	* @return &$this
	* @throws VariableRequiredException
	**/
	protected function &setModsStr($modStr): static {
		$this->_modStr = REQUIRED_VAR($modStr);
		$this->parseMods();
		return $this;
	}

	/**
	* Get string of Modifiers.
	*
	* @return string
	**/
	public function &getModsStr(): string {
		return implode('', $this->_modArr);
	}

	/**
	* Get Modifiers.
	*
	* @return integer
	**/
	public function &getMods(): int {
		return $this->_mod;
	}

	/**
	* Set Modifiers.
	*
	* @param integer	$mods. Modifiers to set.
	* @return &$this
	**/
	public function &setMods($mods): static {
		$this->_mod &= $mods;
		$this->parseMods(false);
		return $this;
	}

	/// Private and Protected methods ///

	/**
	* Parse modifiers from string. 1 char on mod.
	*
	* @param bool (true) $direction
	*	True	- from string $this->_modStr
	*	False	- from integer $this-_mod
	* @return &this
	* @throws VariableRangeException
	**/
	protected function &parseMods($direction = true): static {
		if ($direction){
			$this->_mod = 0;
				for($i=0; $i < strlen($this->_modStr); $i++){
					if (in_array($this->_modStr[$i], array_keys(self::$MODS))){
						$this->_mod |= self::$MODS[$this->_modStr[$i]];
						array_push($this->_modArr, $this->_modStr[$i]);
					}
					else throw new VariableRangeException('Unknown modifier - '.$this->_modStr[$i]);
				}
		}
		else{//Now correct array-values
			foreach (self::$MODS as $key => $M){
				if ($this->isMod($M) and !in_array($key, $this->_modArr)){
					array_push($this->_modArr, $M);
					$this->_modStr .= $M;
				}
				elseif (!$this->isMod($M) and in_array($key, $this->_modArr)){
					$k = array_keys($this->_modArr, $key);
					unset($this->_modArr[$k[0]]);
					$this->_modStr = str_replace($key, '', $this->_modStr);
				}
			}
		}

		//In modified mods - must recalculate values
		$this->_realValued = false;
		$this->_resStr = null;

		return $this;
	}

	/**
	* Treat ->_name as property-name
	*
	* @return string
	**/
	protected function mod_s(): string {
		if (!$this->_realValued){
			$this->_realValue = @$this->_value->{$this->_name};
			$this->_realValued = true;
		}
		else $this->_realValue = $this->_value->{$this->_realValue};
		return (string)$this->_realValue;
	}

	/**
	* Tread ->_name as index in ->_value
	*
	* @return string
	**/
	protected function mod_a(): string {
		if (!$this->_realValued){
			$this->_realValue = $this->_value[$this->_name];
			$this->_realValued = true;
		}
		return (string)$this->_realValue;
	}

	/**
	* Process ->_value through NON_EMPTY_STR. ->_format must have appropriate values.
	*
	* @return string
	**/
	protected function mod_n(): string {
		$result = NON_EMPTY_STR($this->getValue(), @$this->_format[0], @$this->_format[1], @$this->_format[2]);
		return (string)$result;
	}

	/**
	* Process ->_value through standard sprintf function. All elements self::sprintf_var (def: __vAr__) in ->_format replaced by its
	* real value, and this array go in sprintf
	*
	* @return string
	**/
	protected function mod_p(): mixed {
		//Replace by real value.
		foreach (array_keys($this->_format, self::sprintf_var) as $key){
			$this->_format[$key] = $this->_realValue;
		}
		return call_user_func_array('sprintf', $this->_format);
	}

	/**
	* Evaluate. Evaluated only ->_value
	*
	* @return string
	**/
	protected function mod_e(): string {
		if (!$this->_realValued){
			eval('$this->_realValue = '.$this->_name.';');
			$this->_realValued = true;
		}
		else eval('$this->_realValue = '.$this->_realValue.';');
		return (string)$this->_realValue;
	}

	/**
	* Evaluate full! Evaluate all as full result.
	*
	* @return mixed
	**/
	protected function mod_EE(): mixed {
		${self::evaluate_var} = $this->getValue();
		eval('$ret = '.$this->_format[0].';');
		return $ret;
	}

	/**
	* Value instead name
	*
	* @return string
	**/
	protected function mod_v(): string {
		if (!$this->_realValued){
			$this->_realValue = $this->_value;
			$this->_realValued = true;
		}
		else{
			throw new HuFormatException('Got conflicted format modifiers!');
		}
		return (string)$this->_realValue;
	}

	/**
	* ALL. Recursive parse format
	*
	* @return string
	**/
	protected function mod_AA(): string {
		$hf = new self(null, $this->_value, $this->_key);
		$ret = '';
		foreach ($this->_format as $f){
			$hf->setFormat($f);
			$ret .= $hf->getString();
		}
		return $ret;
	}

	/**
	* Iterate by ->_value or ->_realValue.
	*
	* @return string
	**/
	protected function mod_II(): string {
		$t = false; // Create variable first
		$hf = new self($this->_format, $t, $this->_key);
		$ret = '';

		$value = $this->getValue();
		if (\is_iterable($value)) {
			foreach ($value as $key => $v){
				$hf->setValue($v);
				$hf->_key = $key; //Only for I useful
				$result = $hf->getString();
				if ($result !== null) {
					$ret .= $result;
				}
			}
		}
		return $ret;
	}

	/**
	* Get Key of current iteration of I:::.
	*
	* @return string
	**/
	protected function mod_k(): string {
		$this->_realValue = $this->_key;
		$this->_realValued = true;
		return (string)$this->_realValue;
	}
	/**
	* As we overload getString() without arguments, implementation from HuError
	* is not suitable. So, overload it as and thrown exception (class by autoload) to avoid accidentally usages.
	* @TODO It is very useful methods. Consider implementation in the future.
	**/
	public function strToFile($format = null): mixed { throw new ClassMethodException('Method strToFile is not exists yet'); }
	public function strToWeb($format = null): mixed { throw new ClassMethodException('Method strToWeb is not exists yet'); }
	public function strToConsole($format = null): mixed { throw new ClassMethodException('Method strToConsole is not exists yet'); }
	public function strToPrint($format = null): mixed { throw new ClassMethodException('Method strToPrint is not exists yet'); }
}
