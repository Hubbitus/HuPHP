<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\Vars\OutExtraDataBacktrace;

// HuFormatException is defined in separate file Debug/HuFormatException.php

/**
* Class to format different structures.
* @example HuFormat.example.php
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created  2009-03-13 19:01
**/
class HuFormat extends HuError {
	/** Replace this in ->_format on real value of _value (after process mod_s) **/
	public const string sprintf_var = '__vAr__';

	/** Var to process in eval-string in mod_e. In eval string off course witch sign $. **/
	public const string evaluate_var = 'var';

	/** Separator to separate mods from name in one string. For more info see {@see ::parseModsName()} **/
	public const string mods_separator = ':::';

	/**
	* For each present modifier will maintain method of implementation..
	*
	* @var array<string, \Closure>
	**/
	public static array $MODS;

	/**
	* Initialize $MODS with Closure methods.
	**/
	private static function initMODS(): void {
		if (isset(self::$MODS)) {
			return;
		}
		self::$MODS = [
			/**
			* ALL modifier - recursively processes all sub-formats.
			* Format: ['A:::', [sub-format1], [sub-format2], ...]
			**/
			'A' => function(self $obj): string {
				// Avoid infinite recursion with Backtrace/OutExtraDataBacktrace
				if ($obj->_value instanceof Backtrace) {
					return \sprintf('Backtrace[%d calls]', $obj->_value->length());
				}
				if ($obj->_value instanceof OutExtraDataBacktrace) {
					return 'OutExtraDataBacktrace';
				}
				$hf = new self(null, $obj->_value, $obj->_key);
				$ret = '';
				if (\is_array($obj->_format)) {
					foreach ($obj->_format as $f) {
						$hf->setFormat($f);
						$ret .= $hf->getString();
					}
				}
				return $ret;
			},
			/**
			* Setting - access object property by name.
			* Format: ['s:::propertyName']
			**/
			's' => function(self $obj): string {
				if (!$obj->_realValued) {
					/** @phpstan-ignore property.dynamicName */
					$obj->_realValue = @$obj->_value->{$obj->_name};
					$obj->_realValued = true;
				}
				else {
					/** @phpstan-ignore property.dynamicName */
					$obj->_realValue = $obj->_value->{$obj->_realValue};
				}
				return (string)$obj->_realValue;
			},
			/**
			* Array - access array element by key.
			* Format: ['a:::keyName']
			**/
			'a' => function(self $obj): string {
				if (!$obj->_realValued) {
					$obj->_realValue = $obj->_value[$obj->_name];
					$obj->_realValued = true;
				}
				return (string)$obj->_realValue;
			},
			/**
			* Non-empty string - returns formatted string or default value.
			* Format: ['n:::', 'prefix', 'suffix']
			**/
			'n' => function(self $obj): string {
				return Vars::surround($obj->getValue(), @$obj->_format[0], @$obj->_format[1], @$obj->_format[2]);
			},
			/**
			* Sprintf - formats value using sprintf.
			* Format: ['p:::', 'format string']
			**/
			'p' => function(self $obj): mixed {
				//Replace by real value.
				$format = $obj->_format;
				foreach (\array_keys($format, self::sprintf_var, true) as $key) {
					$format[$key] = $obj->_realValue;
				}
				return \call_user_func_array('sprintf', $format);
			},
			/**
			* Evaluate name - evaluates only the name part.
			* Format: ['e:::PHP expression with $var']
			**/
			'e' => function(self $obj): string {
				if (!$obj->_realValued) {
					${self::evaluate_var} = $obj->getValue(); /* @phpstan-ignore variable.dynamicName */
					eval('$obj->_realValue = '.$obj->_name.';');
					$obj->_realValued = true;
				}
				else {
					${self::evaluate_var} = $obj->getValue(); /* @phpstan-ignore variable.dynamicName */
					eval('$obj->_realValue = '.$obj->_realValue.';');
				}
				return (string)$obj->_realValue;
			},
			/**
			* Evaluate full - evaluates entire format as PHP code.
			* Format: ['E:::', 'PHP code with $var']
			**/
			'E' => function(self $obj): mixed {
				/** @phpstan-ignore variable.dynamicName */
				${self::evaluate_var} = $obj->getValue();
				eval('$ret = '.$obj->_format[0].';');
				/** @phpstan-ignore variable.undefined */
				return $ret;
			},
			/**
			* Value - returns the value itself.
			* Format: ['v:::']
			**/
			'v' => function(self $obj): string {
				if (!$obj->_realValued) {
					$obj->_realValue = $obj->_value;
					$obj->_realValued = true;
				}
				else {
					throw new HuFormatException('Got conflicted format modifiers!');
				}
				// Avoid infinite recursion when value is Backtrace or OutExtraDataBacktrace
				if ($obj->_realValue instanceof Backtrace) {
					return \sprintf('Backtrace[%d calls]', $obj->_realValue->length());
				}
				if ($obj->_realValue instanceof OutExtraDataBacktrace) {
					return 'OutExtraDataBacktrace';
				}
				return (string)$obj->_realValue;
			},
			/**
			* Iterate - iterates over array/object and applies format to each element.
			* Format: ['I:::', [format for each element]]
			**/
			'I' => function(self $obj): string {
				$t = false; // Create variable first
				$hf = new self($obj->_format, $t, $obj->_key);
				$ret = '';

				$value = $obj->getValue();
				if (\is_iterable($value)) {
					foreach ($value as $key => $v) {
						$hf->setValue($v);
						$hf->_key = $key; //Only for I useful
						$result = $hf->getString();
						if ($result !== '') {
							$ret .= $result;
						}
					}
				}
				return $ret;
			},
			/**
			* Key - returns current iteration key.
			* Format: ['k:::']
			**/
			'k' => function(self $obj): string {
				$obj->_realValue = $obj->_key;
				$obj->_realValued = true;
				return (string)$obj->_realValue;
			},
		];
	}

	private $_format;				//Array of format.
	private $_modStr;				//Modifiers.
	private $_mods = [];			// Array of modifier letters
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
	public function __construct(?array $format = null, &$value = null, $key = null) {
		self::initMODS();
		parent::__construct();
		$this->set($format, $value, $key);
	}

	/**
	* Set main: format and value.
	*
	* @param	array|string|null	$format. If === null, skipped to allow set other
	*	parts. To clear you may use false/true or any else such as empty string.
	* @param	mixed	$value.	{@see ::setValue()} Skipped if === null. You
	*	may call {@see ::setValue()} to do that
	* @param	mixed	$key	Key of iteration in mod_I and/or mod_A.
	* @return	$this
	**/
	public function &set($format = null, &$value = null, $key = null): static {
		if (null !== $value) {
			$this->setValue($value);
		}
		if (null !== $format) {
			$this->setFormat($format);
		}
		$this->_key = $key;
		return $this;
	}

	/**
	* Return current value.
	*
	* @return mixed
	**/
	public function &getValue(): mixed {
		if ($this->_realValued) {
			return $this->_realValue;
		}
		else {
			return $this->_value;
		}
	}

	/**
	* Set value
	*
	* @param	mixed	$value.	Value to format.
	*	If === null $this->_value =& $this; $this->_realValue =& $this->_value;
	* @return $this
	**/
	public function &setValue(&$value): static {
		if(null === $value) {
			$this->_value =& $this;
		}
		else {
			$this->_value = $value;
		}
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
	* @return $this
	**/
	public function &setFormat($format): static {
		$this->_mods = [];
		$this->_modStr = $this->_name = $this->_resStr = $this->_realValue = null;
		$this->_modArr = [];
		$this->_realValued = false;

		if (\is_array($format)) {
			if (\is_array($format[\key($format)])) {//<2>
				$this->parseModsName(\key($format));
				$this->_format = $format[\key($format)];
			}
			else {//<1>
				$this->parseModsName(\array_shift($format));
				$this->_format = $format;//Tail
			}
		}
		else {//<3>
			//Parse string format for modifiers
			$this->parseModsName($format);
			//If no modifiers, treat as plain string value
			if ($this->_modArr === []) {
				$this->_realValue = $format;
				$this->_realValued = true;
			}
		}

		return $this;
	}

	/**
	* Parses and set from given str. As separator used {@see self::mods_separator}.
	* F.e.: 'AI:::line'. If separator not present - whole string in NAME!
	*
	* @param string|int $str
	* @return $this
	**/
	protected function &parseModsName($str): HuFormat {
		$str = (string) $str;
		if (!\strstr($str, self::mods_separator)) {//Whole name
			$this->_name = $str;
			$this->_modStr = '';
		}
		else {//Separator present
			list($this->_modStr, $this->_name) = \explode(self::mods_separator, $str);
		}
		return $this->parseMods();
	}

	/**
	* Construct and return string to represent provided value according given format.
	*
	* @param array|null $fields Fields to include in the string representation (for compatibility with parent class)
	* @return string
	**/
	public function getString(?array $fields = null): string {
		if ($this->_resStr === null) {
			$this->_resStr = '';

			foreach ($this->_modArr as $mod) {
				$method = self::$MODS[$mod];
				$result = $method($this);
				if ($result !== null) {
					$this->_resStr .= $result;
				}
			}

			//If all mod_* are only evaluate value and not produce out.
			if ($this->_resStr === '') {
				$value = $this->getValue();
				if ($value === null) {
					return '';
				}
				if (\is_array($value)) {
					return \print_r($value, true);
				}
				if (\is_object($value) && !\method_exists($value, '__toString')) {
					return \print_r((array)$value, true);
				}
				return (string)$value;
			}
		}

		return $this->_resStr;
	}

	/**
	* Check if mod present.
	*
	* @param string	$mod.
	* @return bool
	**/
	public function isMod(string $mod): bool {
		if ($this->_mods === [] && $this->_modStr) {
			$this->parseMods();
		}
		return \in_array($mod, $this->_mods, true);
	}

	/**
	* Set, or unset mods.
	*
	* @param string	$mods. String to set o unset Mods like: '-I+s+n'.
	*	If '-' - unset.
	*	If '+' - set.
	*	If '*' - invert.
	*	If absent - equal to '+'
	* @return $this
	* @throws VariableRangeException
	**/
	public function &changeModsStr(string $mods): static {
		$len = \strlen($mods);
		for($i=0; $i < $len; $i++) {
			if (\in_array($mods[$i], ['+', '-', '*'], true)) {
				$op = $mods[$i];
				++$i;
				if ($i >= $len) {
					throw new VariableRangeException('Modifier expected after operator "'.$op.'"');
				}
				$mod = $mods[$i];
			}
			else {
				$mod = $mods[$i];
				$op = '+';	//Default
			}

			if (!isset(self::$MODS[$mod])) {
				throw new VariableRangeException('Unknown modifier - "'.$mod.'"');
			}

			switch ($op) {
				case '+':
					if (!\in_array($mod, $this->_mods, true)) {
						$this->_mods[] = $mod;
					}
					break;

				case '-':
					$this->_mods = \array_values(\array_filter($this->_mods, fn($m) => $m !== $mod));
					break;

				case '*':
					if (\in_array($mod, $this->_mods, true)) {
						$this->_mods = \array_values(\array_filter($this->_mods, fn($m) => $m !== $mod));
					} else {
						$this->_mods[] = $mod;
					}
					break;

					// No defaults - there is set default for $op before, so there impossible other values
			}
		}

		$this->_modStr = \implode('', $this->_mods);
		$this->parseMods();
		return $this;
	}

	/**
	* Get string of Modifiers.
	*
	* @return string
	**/
	public function getModsStr(): string {
		return \implode('', $this->_modArr);
	}

	/**
	* Parse modifiers from string. 1 char on mod.
	*
	* @return $this
	* @throws VariableRangeException
	**/
	protected function &parseMods(): static {
		$this->_mods = [];
		for($i=0; $i < \strlen($this->_modStr); $i++) {
			$mod = $this->_modStr[$i];
			if (isset(self::$MODS[$mod])) {
				$this->_mods[] = $mod;
				$this->_modArr[] = $mod;
			}
			else {
				throw new VariableRangeException("Unknown modifier [{$mod}]");
			}
		}

		//In modified mods - must recalculate values
		$this->_realValued = false;
		$this->_resStr = null;

		return $this;
	}
}
