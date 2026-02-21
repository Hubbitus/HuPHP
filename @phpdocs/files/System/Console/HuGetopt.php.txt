<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System\Console;

/**
* Console package to parse parameters in CLI-mode
*
* @package Console
* @subpackage Getopt
* @version 0.1.3
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 15:52 v 0.1.1 to 0.1.2
*
* @uses REQUIRED_VAR()
* @uses VariableRequiredException
**/

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException;
use Hubbitus\HuPHP\RegExp\RegExpPcre;
use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Vars\Settings\SettingsGet;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;
use function Hubbitus\HuPHP\Macroses\EMPTY_VAR;


/**
* First there was a try to use http://pear.php.net/Console_getopt. Created Hu_Console_Getopt
* extending pear class. But it is very limited:
*	1. No way to bind short options to long
*	2. No way get value presented by long OR short options. They logic only outside.
*	3. Options provided after non optioned arguments - completely ignored.
*	4. In case when one option provided more than once - only last value present, other lost.
*
* Adjust PEAR Console_getopt is very difficult, so, write self version.
*
* In most cases behavior this class is same as described in GNU "man 3 getopt", with several exceptions-additionally:
*	1) Format of incoming options (`optstring` by GNU man) is different, more flexible allow associate short option with long!
*	2) Don't support GNU extension -W
*	3) Environment variable POSIXLY_CORRECT not handled, and behavior always same as GNU default (first +/- in optstring modes too not handled!!!)
*	4) Additionally in settings moved 'long_start' ('--') and 'short_start' ('-') and may be changed if you want.
* 		Even more, it is array, and may contain any amount of element. It is useful, if you, for example, wish use '-' and '+' in short options.
*	5) PHP-CLI self do NOT correctly handle long options with sign "=" form or without space:
*		--longOpt 'optarg' - correct
* 		--longOpt='optarg' - In $argv placed full, not correct exploded to opt and optarg.
*		--longOpt'optarg' - In $argv placed full, not correct exploded to opt and optarg.
*		GuGetopt correct handle all this cases.
*	6) Also PHP-CLI does NOT handle short options in clue form (F.e. -o -t -f -s - does, -otfs - NOT). So, HuGetopt - handle it properly!
**/
class HuGetopt extends SettingsGet {
	/**
	* Array of raw arguments to parse
	* @var array
	*/
	private $argv;

	/**
	* Parsed options (what provided to parse arguments of command line).
	* @var array
	**/
	private $_opts;

	/**
	* Non-option arguments (all other)
	* @var HuArray
	**/
	private $_nonOpts;

	/**
	* Parsed arguments from command line.
	* @var array
	**/
	private $_args;

	/**
	* Short and long and long arrays cache. Only fore speedup, otherwise need iterate each time ti find needed.
	* @var array
	**/
	private $_optsL;
	private $_optsS;

	private $_curArgv = 0;	//Current index.
	private $_curArg;		//Current arg, if needed correction on real.

	/**
	* Construct
	*
	* @param	array	$opts. Options to set. {@see ::setOpts()}
	* @param 	HuGetoptSettings	$sets=null. Settings. If null - instanced default.
	**/
	public function __construct(array $opts, HuGetoptSettings $sets = null){
		$this->_sets = EMPTY_VAR($sets, new HuGetoptSettings);
		$this->setOpts($opts);
		$this->_nonOpts = new HuArray();
	}

	/**
	* Set allowed options to parse.
	* $opts array of options, which have format:
	*	array(
	*		's',	//Short option
	* 		'long',	//Long option
	* 		'mods'	//Modifiers
	*	)
	* Where mods mean:
	*	':' - Must have value.
	*	'::'- May have value.
	*
	* @param	array	$opts. Options to set.
	* @return	&$this
	* @throws VariableRequiredException
	**/
	public function &setOpts(array $opts): static {
		$this->_optsS = $this->_optsL = $this->_opts = array();
		foreach (REQUIRED_VAR($opts) as $k => $opt){
			$this->_opts[$k]	= new HuGetoptOption(
				$this->sets()->HuGetopt_option_options
				,array(
					'OptS' 	=> $opt[0],
					'OptL'	=> @$opt[1],
					'Mod'	=> (string)@$opt[2]
				)
			);
			$this->_optsS[$opt[0]]	= $k;
			if (@$opt[1])
				$this->_optsL[$opt[1]]	= $k;
		}
		return $this;
	}

	/**
	* Return Object(HuGetopt_option) by its string 'w', or 'what'
	*
	* @param	string	$str
	* @param	string  $type=a	$type. Possibles: s|l|a
	*	s - Short
	*	l - Long
	*	a and any other!!- ('a' by default) Make assumption by length $str - if strlen($str) == 1 - short, other - long
	* @return HuGetoptOption
	**/
	public function &getOptByStr(string $str, string $type = 'a'): HuGetoptOption {
		switch ($type){
			case 's':
				$type =& $this->_optsS;
				break;

			case 'l':
				$type =& $this->_optsL;
				break;

			default:
				if (1 == \strlen($str)) $type =& $this->_optsS;
				else $type =& $this->_optsL;
				break;
		}
		return $this->_opts[ $type [$str] ];
	}

	/**
	* Main Horse!!! Doing most work.
	*
	* @throws VariableRequiredException
	**/
	public function parseArgs(): void {
		$this->_nonOpts->push($this->currentArg());
		while($cArg = $this->nextArg()){
			if ( '--' == ($cArg) ){
				$this->_nonOpts->pushArray(\array_splice($this->nextArg(), $this->_curArgv));
				break;
			}

			if ( !($o = $this->isOpt($cArg)) ){
				$this->_nonOpts->push($cArg);
				continue;
			}

		//reference. All modification - in-place.
		$o = $this->getOptByStr($o->Opt->{0}, $o->OptT->{0})->add($o);

			if('' == $o->Mod){
				$o->Val->_last_ = true;
			}
			else{//: or ::
				$optarg = $o->Val->_last_; //def
				if (
					!$o->Val->count()	//If NOT long option '=' form
					 and
					( ( false !== ($optarg = $this->nextArg())) and false === $this->isOpt($optarg) ) //And next NOT arg of current option
					){

						if('::' == $o->Mod){//Mandatory argument for option
							throw new VariableRequiredException(new Backtrace(), 'Option [' . $o->Opt->_last_ . '] requires argument!');
						}
					}
				$o->Val->_last_ = $optarg;
			}
		}
	}

	/**
	* Move internal pointer to next arg, and return it.
	*
	* @return	string
	**/
	protected function nextArg(){
		if ($this->_curArg){
			$tmp = $this->_curArg;
			$this->_curArg = null;
			return $tmp;
		}
		elseif(++$this->_curArgv < \sizeof($this->argv)){
			return $this->argv[$this->_curArgv];
		}
		else return false;
	}

	/**
	* Return current argument
	*
	* @return	string
	**/
	protected function currentArg(){
		if ($this->_curArg){
			$tmp = $this->_curArg;
			$this->_curArg = null;
			return $tmp;
		}
		else return $this->argv[$this->_curArgv];
	}

	/**
	* Return option or not $arg.
	*
	* @param	string	$arg. Usaly element of $argv
	* @return
	**/
	protected function isOpt($arg){
		return ( ($r =& $this->isShortOpt($arg)) ? $r : $this->isLongOpt($arg) );
	}

	/**
	* Check if arg is short option.
	*
	* @param	string	$arg. Arg-string to check
	* @return	HuGetoptOption. In object ->Val NOT filled. For exception see description {@see ::isLongOpt()}
	**/
	public function isShortOpt($arg){
		$re = new RegExpPcre(
			( $reg = '/^('.implode('|', RegExpPcre::quote($this->sets()->start_short)).')('.implode('|', array_keys($this->_optsS)).')(.*)/s' ),
			$arg
		);
		$re->doMatch();

		if ($re->matchCount()){
			//Handle sequence of short options without opt-arguments. E.g. `-otfs`.
			if ($o = $this->getOptByStr($re->match(2), 's') and (':' == $o->Mod or '::' == $o->Mod) ){//Have optarg
				return new HuGetoptOption(
					$this->sets()->HuGetopt_option_options
					,[
						'Sep'	=> new HuArray($re->match(1)),
						'Opt'	=> new HuArray($re->match(2)),
						'Val'	=> new HuArray(('' !== (string)$re->match(3) ? $re->match(3) : $this->nextArg())),
						'OptT'	=> new HuArray('s')
					]
				);
			}
			else{//Not have optarg => $re->match(2) is continue of non-optarg options.
				if ($re->match(3)) $this->_curArg = '-' . $re->match(3);
				return new HuGetoptOption(
					$this->sets()->HuGetopt_option_options
					,array(
						'Sep'	=> new HuArray($re->match(1)),
						'Opt'	=> new HuArray($re->match(2)),
						'Val'	=> new HuArray( array(null) ),
						'OptT'	=> new HuArray('s')
					)
				);
			}
		}
		return false;
	}

	/**
	* Check if arg is long option
	*	But, BE CAREFUL ->Val will be filled in only one case: See additional
	*	5 in main description of class HuGetopt about bug in php-cli to parse
	*	--longOpt='optarg' and --longOpt'optarg' forms of long options. In
	*	this form, when value of arg in same element of $argv - this it parsed
	*	and filled ->Val with this value, and ->= set to true. In other cases,
	*	next argument not got!
	*
	* @param	string	$arg. Arg-string to check
	* @return	HuGetoptOption.
	**/
	public function isLongOpt($arg): HuGetoptOption {
		$re = new RegExpPcre(
			( $reg = '/^('.implode('|', RegExpPcre::quote($this->sets()->alternative ? array_merge($this->sets()->start_long, $this->sets()->start_short) : $this->sets()->start_long)).')('.implode('|', array_keys($this->_optsL)).')(=|(?>\s*))(.*)/s' ),
			$arg
		);
		$re->doMatch();

		if ($re->matchCount()){
			return new HuGetoptOption(
				$this->sets()->HuGetopt_option_options
				,array(
					'Sep'	=> new HuArray($re->match(1)),
					'Opt'	=> new HuArray($re->match(2)),
					'='		=> new HuArray($re->match(3)),
					'Val'	=> new HuArray(($re->match(4) ? $re->match(4) : $this->nextArg())),
					'OptT'	=> new HuArray('l')
				)
			);
		}
		return false;
	}

	/**
	* Set new array of arguments
	*
	* @param array	$argv
	* @return	&$this
	**/
	public function &setArgv(array $argv): static {
		$this->argv = $argv;
		return $this;
	}

	/**
	* Short alias for {@see ::getOptByStr()}
	*
	* @param mixed	$opt
	* @param mixed('a')	$type
	* @return HuGetoptOption	$this->getOptByStr()
	**/
	public function get($opt, $type = 'a'): HuGetoptOption {
		return $this->getOptByStr($opt, $type);
	}
	/**
	* Object(HuArray) of NonOption arguments. all, 0 - by default is name of self script!
	*
	* @param integer(0)	$from. Start from element. Very usfull value 1, to ignore skript-name.
	* @return HuArray.
	**/
	public function getNonOpts($from = 0): HuArray {
		return $this->_nonOpts->getSlice($from);
	}
	/**
	 * Return array known (defined for parsing, not parsed!) short options.
	 *
	 * @return array
	 */
	public function getListShortOpts(): array {
		return $this->_optsS;
	}
	/**
	 * Return array known (defined for parsing, not parsed!) long options.
	 *
	 * @return array
	 */
	public function getListLongOpts(): array {
		return $this->_optsL;
	}
	/**
	* Idea (and method name) got from PEAR Console_getopt and adopted, modified.
	* Safely read the $argv PHP array across different PHP configurations.
	* Will take care on register_globals and register_argc_argv ini directives
	*
	* @return &this;
	* @throws VariableEmptyException
	**/
	public function &readPHPArgv(){
		global $argv;

		if (is_array($argv)) $this->setArgv($argv);
		elseif (@is_array($_SERVER['argv'])) $this->setArgv($_SERVER['argv']);
		elseif (@is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) $this->setArgv($GLOBALS['HTTP_SERVER_VARS']['argv']);
		else throw new VariableEmptyException(new Backtrace(), "readPHPArgv(): Could not read cmd args (register_argc_argv=Off?)");

		return $this;
	}
}
