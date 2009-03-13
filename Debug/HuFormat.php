<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuFormat
* @version 2.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-13 19:01 ver 2.0b to 2.1
*	- Add mod_k (k modifier) and support infrastrukture for it, such as save it acsorr mod_A and mod_I.
**/

include_once('Exceptions/variables.php');

include_once('Debug/HuError.php');
include_once('macroses/EMPTY_STR.php');
include_once('macroses/ASSIGN_IF.php');
include_once('macroses/REQUIRED_VAR.php');

include_once('Vars/Singleton.php');

include_once('System/OS.php');

class HuFormatException extends VariableException{}

class HuFormat extends HuError{
	/** Replace this in ->_format on real value of _value (after process mod_s) **/
	const sprintf_var = '__vAr__';
	/** Var to process in eval-string in mod_e. In eval string off course witch sign $. **/
	const evalute_var = 'var';

	/** Separator to separate mods from name in one string. For more info see {@see ::parseModsName()} **/
	const mods_separator = ':::';

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
	static public $MODS = array(
		'A'	=> 1,	#ALL. Exclusive, all other modifiers not processed. Each process as HuFormat.
		's'	=> 2,	#Setting
		'a'	=> 4,	#Array
		'n'	=> 8,	#Non_empty_str
		'p'	=> 16,	#sPrintf. {@link http://php.net/sprintf}
		'e'	=> 32,	#Evaluate. Evaluated only ->_name !!!
		'E'	=> 64,	#Evaluate full! Evaluate all as full result.
		'v'	=> 128,	#Value,
		'I'	=> 256,	#Iterate ->_value (or ->_realValue) and each format as ->_format
		'k'	=> 512,	#Key. Get key of current iteration of I:::.
	);

	private $_format;				#Array of format.
	private $_modStr;				#Modifiers.
	private $_mod;					#Integer of present mods
	private $_modArr = array();		#Array of present mods
	private $_value;				#Value, what processed in this formating.
	private $_realValue;			#If modified (part) in mod_s, mod_a
	private $_realValued = false;		#Flag, to allow pipe through several mods (like as s. a, e)
	private $_name;
	private $_key;					#Key from mod_I itaration for the mod_k

	private $_resStr;				#For caching

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
/*
	//Unfortunately a can not Use multiple inheritance. Inherit get_settings is more graceful way.
	runkit_method_copy(__CLASS__, 'sets', 'get_settings', 'sets');
	runkit_method_copy(__CLASS__, 'create', 'Single', 'create');
*/
	}#m __construct

	/**
	* Set main: format and value.
	*
	* @param array|string	$format
	* @param &mixed	$value.	{@see ::setValue()}
	* @return	&$this
	**/
	public function &set($format = null, &$value = null, $key = null){
	$this->setValue($value);

		if (null !== $format) $this->parseInputArray($format);
	$this->_key = $key;
	return $this;
	}#m set

	/**
	* Return current value.
	*
	* @return &mixed
	**/
	public function &getValue(){
		if ($this->_realValued) return $this->_realValue;
		else return $this->_value;
	}#m getValue

	/**
	* Set value
	*
	* @param &mixed	$value.	Value to format.
	*	If === null $this->_value =& $this; $this->_realValue =& $this->_value; 	 
	* @return &$this
	**/
	public function &setValue(&$value){
		if(null === $value){
		$this->_value =& $this;
		}
		else $this->_value = $value;
	$this->_realValued = false;
	$this->_resStr = null;
	return $this;
	}#m setValue

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
	public function &parseInputArray($format){
	$this->_mod = 0;
	$this->_modStr = $this->_name = $this->_resStr = $this->_realValue = null;
	$this->_modArr = array();
	$this->_realValued = false;

		if (is_array($format)){
			if (is_array($format[key($format)])){#<2>
			$this->parseModsName(key($format));
			$this->_format = $format[key($format)];
			}
			else{#<1>
			$this->parseModsName(array_shift($format));
			$this->_format = $format;	#Tail
			}
		}
		else{#<3>
		$this->_name = $this->_realValue = $format;
		$this->_realValued = true;
		}

	return $this;
	}#m parseInputArray

	/**
	* Parses and set from given str. As separator used {@see self::mods_separator}.
	* F.e.: 'AI:::line'. If separator not present - whole string in NAME!
	*
	* @param string $str
	* @return &$this
	**/
	protected function &parseModsName($str){
		if (!strstr($str, self::mods_separator)){//Whole name
		$this->_name = $str;
		$this->_modStr = '';
		}
		else{//Separator present
		list ($this->_modStr, $this->_name) = explode(self::mods_separator, $str);
		}
	return $this->parseMods(true);
	}#m parseModsName

	/**
	* Construct and return string to represent provided value according given format.
	*
	* @return string
	**/
	public function getString(){
		if (!$this->_resStr){
		$this->_resStr = '';

			foreach ($this->_modArr as $mod){
				if (ctype_upper($mod)){
				$this->_resStr .= call_user_func(array($this, 'mod_'.$mod.$mod));
				}
				else $this->_resStr .= call_user_func(array($this, 'mod_'.$mod));
			}

			//If all mod_* are only evalute value and not produce out.
			if (!$this->_resStr) return $this->getValue();
		}

	return $this->_resStr;
	}#m getString

	/**
	* Set or not?
	*
	* @param integer	$mod.
	* @return boolean
	**/
	public function isMod($mod){
		if (!$this->_mod and $this->_modstr) $this->parseMods();
	return ($this->_mod & $mod);
	}#m isMod

	/**
	* Set, or unset mods.
	*
	* @param string	$mods. String to set o unset Mods like: '-I+s+n'.
	*	If '-' - unset.
	*	If '+' - set.
	*	If '*' - invert.
	*	If absent - equal to '+'
	* @return &$this
	* @Throw(VariableRangeException)
	**/
	public function &changeModsStr($mods){
		for($i=0; $i < strlen($mods); $i++){
			if (in_array($mods{$i}, array('+', '-', '*'))){
			$op = $mods{$i};
			$mod = $mods{++$i};
			}
			else{
			$mod = $mods{$i};
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
	}#m changeModsStr

	/**
	* Set Modifiers from string.
	*
	* @param string	$modstr	String of modifiers.
	* @return &$this
	* @Throw(VariableRequiredException)
	**/
	protected function &setModsStr($modstr){
	$this->_modStr = REQUIRED_VAR($modstr);
	$this->parseMods();
	return $this;
	}#m setModsStr

	/**
	* Get string of Modifiers.
	*
	* @return string
	**/
	public function &getModsStr(){
	return implode('', $this->_modArr);
	}#m getModsStr

	/**
	* Get Modifiers.
	*
	* @return integer
	**/
	public function &getMods(){
	return $this->_mod;
	}#m setMods

	/**
	* Set Modifiers.
	*
	* @param integer	$mods. Modifiers to set. 
	* @return &$this
	**/
	public function &setMods($mods){
	$this->_mod &= $mods;
	$this->parseMods(false);
	return $this;
	}#m setMods

	/**##########################################################
	* Private and Protected methods
	##########################################################**/

	/**
	* Parse modifiers from string. 1 char on mod.
	*
	* @param bolean(true)	$direction
	*	True	- from string $this->_modStr
	*	False	- from integer $this-_mod
	* @return &this
	* @Throw(VariableRangeException)
	**/
	protected function &parseMods($direction = true){
		if ($direction){
		$this->_mod = 0;
			for($i=0; $i < strlen($this->_modStr); $i++){
				if (in_array($this->_modStr{$i}, array_keys(self::$MODS))){
				$this->_mod |= self::$MODS[$this->_modStr{$i}];
				array_push($this->_modArr, $this->_modStr{$i});
				}
				else throw new VariableRangeException('Unknown modifier - '.$this->_modStr{$i});
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

	//In modifyed mods - must recalculate values
	$this->_realValued = false;
	$this->_resStr = null;

	return $this;
	}#m parseMods

	/**
	* Treat ->_name as property-name
	*
	* @return void
	**/
	protected function mod_s(){
		if (!$this->_realValued){
		$this->_realValue = @$this->_value->{$this->_name};
		$this->_realValued = true;
		}
		else $this->_realValue = $this->_value->{$this->_realValue};
	}#m mod_s

	/**
	* Tread ->_name as index in ->_value
	*
	* @return void
	**/
	protected function mod_a(){
		if (!$this->_realValued){
		$this->_realValue = $this->_value[$this->_name];
		$this->_realValued = true;
		}
		else $this->_realValue = $this->_value[$this->_realValue];
	}#m mod_a

	/**
	* Process ->_value through NON_EMPTY_STR. ->_format must have appropriate values.
	*
	* @return string
	**/
	protected function mod_n(){
	return NON_EMPTY_STR($this->getValue(), @$this->_format[0], @$this->_format[1], @$this->_format[2]);
	}#m mod_n

	/**
	* Procces ->_value through standard sprintf function. All elements self::sprintf_var (def: __vAr__) in ->_format replaced by its
	* real value, and this array go in sprintf
	*
	* @return string
	**/
	protected function mod_p(){
		#Replace by real value.
		foreach (array_keys($this->_format, self::sprintf_var) as $key){
		$this->_format[$key] = $this->_realValue;
		}
	return call_user_func_array('sprintf', $this->_format);
	}#m mod_p

	/**
	* Evalute. Evaluted only ->_value
	*
	* @return void
	**/
	protected function mod_e(){
		if (!$this->_realValued){
		eval('$this->_realValue = '.$this->_name.';');
		$this->_realValued = true;
		}
		else eval('$this->_realValue = '.$this->_realValue.';');
	}#m mod_e

	/**
	* Evaluete full! Evaluete all as full result.
	*
	* @return string
	**/
	protected function mod_EE(){
	${self::evalute_var} = $this->getValue();
	eval('$ret = '.$this->_format[0].';');
	return $ret;
	}#m mod_E

	/**
	* Value instead name
	*
	* @return void
	**/
	protected function mod_v(){
		if (!$this->_realValued){
		$this->_realValue = $this->_value;
		$this->_realValued = true;
		}
		else{
		throw new HuFormatException('Got conflicted format modifiers!');
		}
	}#m mod_e

	/**
	* ALL. Recursive parse format
	*
	* @return string
	**/
	protected function mod_AA(){
	$hf = new self(null, $this->_value, $this->_key);
	$ret = '';
		foreach ($this->_format as $f){
		$hf->parseInputArray($f);
		$ret .= $hf->getString();
		}
	return $ret;
	}#m mod_AA

	/**
	* Iterate by ->_value or ->_realValue.
	*
	* @return string
	**/
	protected function mod_II(){
	$hf = new self($this->_format, $t = false, $this->_key);
	$ret = '';

		foreach ($this->getValue() as $key => $v){
		$hf->setValue($v);
		$hf->_key = $key; //Only for I usefull
		$ret .= $hf->getString();
		}
	return $ret;
	}#m mod_II

	/**
	* Get Key of cunrrent iteration of I:::.
	*
	* @return string
	**/
	protected function mod_k(){
	$this->_realValue = $this->_key;
	$this->_realValued = true;
	}#m mod_k
};#c HuFormat
?>