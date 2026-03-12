<?php
declare(strict_types=1);
namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\Format\PrintoutDefault;
use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableArrayInconsistentException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\System\OutputType;

/**
* BackTraceNode. In array converted to like this. Otherwise each member accessible separately.
* Structure example:
* Array(){
*     [file] => string(37) "/var/www/_SHARED_/Debug/backtrace.php"    //Mandatory
*     [line] => int(47)    //Mandatory
*     [function] => string(11) "__construct"    //Mandatory
*     [class] => string(9) "backtrace"
*     [object] => object(backtrace)#1 (2) { <Full Object> }
*     [type] => string(2) "->"
*     [args] => Array(2){    //Mandatory
*         [0] => NULL
*         [1] => int(0)
*     }
*     //Additional according to standard element of array from debug_backtrace();
*     //Point to number in element of array debug_backtrace();
*     [N] => 1    //Mandatory
* }
*
* @property array<mixed> $args Arguments array
* @property string $file File path
* @property int $line Line number
* @property string $function Function name
* @property string $class Class name
* @property string $type Type indicator
*
* implements Iterator by example from main description http://php.net/manual/ru/language.oop5.iterations.php
**/
class BacktraceNode implements \Iterator {
	/** @var string[] */
	public static array $properties = [
		'file',
		'line',
		'function',
		'class',
		'object',
		'type',
		'args',
		'N'
	];

	/** @var array<mixed>|null */
	private $_btn = null;

	/** @var array<mixed> */
	protected $_format;

	/**
	* Construct object from array
	*
	* @param array<mixed>|null $arr Array to construct from
	* @param mixed $N Number of node, got separately (may be already in $arr).
	**/
	public function __construct(?array $arr = null, $N = false) {
		$this->_btn ??= $arr;
		if (false !== $N) {
			$this->_btn['N'] = $N;
		}
	}

	/**
	* To allow constructions like: backtraceNode::create()->methodName()
	*
	* @param array<mixed>|null $arr {@inheritdoc ::__construct()}
	* @param mixed $N {@inheritdoc ::__construct()}
	* @return self
	**/
	public static function create(?array $arr = null, $N = false): self {
		return new self($arr, $N);
	}

	/**
	* Return property, if it exists, Throw ClassPropertyNotExistsException otherwise
	*
	* @param string $name Name of required property
	* @return mixed Reference on property value
	* @throws ClassPropertyNotExistsException
	**/
	public function &__get($name): mixed {
		if (!\in_array($name, BacktraceNode::$properties, true)) {
			throw new ClassPropertyNotExistsException('Property "' . $name . '" does NOT exist!');
		}
		return $this->_btn[$name];
	}

	/**
	* Check isset of requested property. See http://php.net/isset comment of "phpnotes dot 20 dot zsh at spamgourmet dot com"
	*
	* @param string $name Name of required property
	* @return bool
	**/
	public function __isset($name): bool {
		if (!\in_array($name, BacktraceNode::$properties, true)) {
			throw new ClassPropertyNotExistsException('Property <' . $name . '> does NOT exist!');
		}
		return isset($this->_btn[$name]);
	}

	/**
	* Dump in appropriate(auto) form backtraceNode.
	*
	* @param bool $return
	* @param string $header
	* @return mixed return Dump::a(...)
	**/
	public function dump(bool $return = false, string $header = 'backtraceNode'): mixed {
		return Dump::a($this->_btn, $header, $return);
	}

	/// From interface Iterator ///

	/**
	* Rewind internal pointer to begin
	*
	* @return void
	**/
	public function rewind(): void {
		\reset($this->_btn);
	}

	/**
	* Return current element
	*
	* @return mixed
	**/
	public function current(): mixed {
		return \current($this->_btn);
	}

	/**
	* Return current key
	*
	* @return mixed
	**/
	public function key(): mixed {
		return \key($this->_btn);
	}

	/**
	* Move to next element
	*
	* @return void
	**/
	public function next(): void {
		\next($this->_btn);
	}

	/**
	* Check if current position is valid
	*
	* @return bool
	**/
	public function valid(): bool {
		return ($this->current() !== false);
	}

	/**
	* Compares two nodes by fnmatch() all properties in $node1
	*
	* @param BacktraceNode $toCmp Node to compare to
	* @return int 0 if equals. Other otherwise (> or < not defined, but *may be* done later).
	**/
	public function fnmatchCmp(BacktraceNode $toCmp): int {
		foreach ($toCmp as $key => $prop) {
			// Dynamic property access - comparing properties by name from iterator
			if (
				!isset($this->{$key}) /* @phpstan-ignore property.dynamicName */
				|| !\fnmatch($prop, $this->{$key}) /* @phpstan-ignore property.dynamicName */
			) {
				return 1;
			}
		}
		return 0; // FnmatchEquals!
	}

	/**
	* Set format to formatArgs. Array by type of out as key {@see OS::OUT_* constants}, and values as array in format,
	* as described in {@see class HuFormat}. {@example Debug/_HuFormat.defaults/backtrace::printout.php}
	* On time of set format NOT CHECKED!
	*
	* @param array<mixed> $format
	* @return void
	* @throws VariableRequiredException
	**/
	public function setArgsFormat(array $format): void {
		$this->_format = Vars::requiredNotEmpty($format);
	}

	/**
	* Return string of formatted args
	*
	* @param array<mixed>|null $format If null, trying from ->_format set in {@see ::setArgsFormat()}, and finally get global defined by default in HuFormat $GLOBALS['__CONFIG']['backtrace::printout']
	* @param OutputType|null $outType If present - determine type of format from $format (passed or default). Must be index in $format.
	* @return string
	* @throws VariableArrayInconsistentException
	**/
	public function formatArgs(?array $format = null, ?OutputType $outType = null): string {
		$outType ??= OS::getOutType();

		// Ensure default format is configured
		if (!isset($GLOBALS['__CONFIG']['backtrace::printout']) || $GLOBALS['__CONFIG']['backtrace::printout'] === []) {
			PrintoutDefault::configure();
		}

		// Get format from parameter, or from instance format, or from global config
		if ($format === null) {
			$format = $this->_format[$outType->name]['argtypes']
				?? $GLOBALS['__CONFIG']['backtrace::printout'][$outType->name]['argtypes'];
		}

		$args = '';
		$hf = new HuFormat();

		/** @var array<mixed> $argsArray */
		$argsArray = $this->args;
		foreach ($argsArray as $var) {
			if ($args !== '') {
				$args .= ', ';
			}

			if (isset($format[\gettype($var)])) {
				$form =& $format[\gettype($var)];
			} elseif (isset($format['default'])) {
				$form =& $format['default'];
			} else {
				throw new VariableArrayInconsistentException('Format of type ' . \gettype($var) . ' not found. "default" also not provided in $format');
			}

			$hf->set($form, $var);
			$args .= $hf->getString();
		}
		return $args;
	}
}
