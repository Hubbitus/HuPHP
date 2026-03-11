<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System\Console;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException;
use Hubbitus\HuPHP\RegExp\RegExpPcre;
use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;

/**
* Console package to parse parameters in CLI-mode
*
* First there was a try to use http://pear.php.net/Console_getopt. Created Hu_Console_Getopt
* extending pear class. But it is very limited:
*  1. No way to bind short options to long
*  2. No way get value presented by long OR short options. They logic only outside.
*  3. Options provided after non optioned arguments - completely ignored.
*  4. In case when one option provided more than once - only last value present, other lost.
*
* Adjust PEAR Console_getopt is very difficult, so, write self version.
*
* In most cases behavior this class is same as described in GNU "man 3 getopt", with several exceptions-additionally:
*  1) Format of incoming options (`optstring` by GNU man) is different, more flexible allow associate short option with long!
*  2) Don't support GNU extension -W
*  3) Environment variable POSIXLY_CORRECT not handled, and behavior always same as GNU default (first +/- in optstring modes too not handled!!!)
*  4) Additionally in settings moved 'long_start' ('--') and 'short_start' ('-') and may be changed if you want.
*      Even more, it is array, and may contain any amount of element. It is useful, if you, for example, wish use '-' and '+' in short options.
*  5) PHP-CLI self do NOT correctly handle long options with sign "=" form or without space:
*      --longOpt 'optarg' - correct
*      --longOpt='optarg' - In $argv placed full, not correct exploded to opt and optarg.
*      --longOpt'optarg' - In $argv placed full, not correct exploded to opt and optarg.
*      GuGetopt correct handle all this cases.
*  6) Also PHP-CLI does NOT handle short options in clue form (F.e. -o -t -f -s - does, -otfs - NOT). So, HuGetopt - handle it properly!
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 15:52 v 0.1.1 to 0.1.2
**/
class HuGetopt {
	/**
	* Array of raw arguments to parse
	* @var array<string>
	**/
	private array $argv;

	/**
	* Parsed options (what provided to parse arguments of command line).
	* @var array<int, HuGetoptOption>
	**/
	private array $_opts;

	/**
	* Non-option arguments (all other)
	**/
	private HuArray $_nonOpts;

	/**
	* Short and long and long arrays cache. Only fore speedup, otherwise need iterate each time ti find needed.
	* @var array<string, int>
	**/
	private array $_optsL;

	private array $_optsS;

	/**
	* Settings object
	**/
	protected HuGetoptSettings $_sets;

	private int $_curArgv = 0;
	private ?string $_curArg = null;
	private string|false $_peekedForNext = false;

	/**
	* Get settings object
	* @return HuGetoptSettings
	**/
	public function &getSettings(): HuGetoptSettings {
		return $this->_sets;
	}

	/**
	* Construct
	*
	* @param array<int, array<int, string>> $opts Options to set. {@see ::setOpts()}
	* @param HuGetoptSettings|null $sets Settings. If null - instanced default.
	**/
	public function __construct(array $opts, ?HuGetoptSettings $sets = null) {
		$this->_sets = $sets ?? new HuGetoptSettings();
		$this->setOpts($opts);
		$this->_nonOpts = new HuArray();
	}

	/**
	* Set allowed options to parse.
	* $opts array of options, which have format:
	* [
	*     's',    // Short option
	*     'long', // Long option
	*     'mods'  // Modifiers
	* ]
	* Where mods mean:
	*  ':'  - Must have value.
	*  '::' - May have value.
	*
	* @param array<int, array<int, string>> $opts Options to set.
	* @return $this
	* @throws VariableRequiredException
	**/
	public function &setOpts(array $opts): static {
		$this->_optsS = $this->_optsL = $this->_opts = [];
		foreach ($opts as $k => $opt) {
			$this->_opts[$k] = new HuGetoptOption(
				$this->getSettings()->HuGetopt_option_options,
				[
					'OptS' => $opt[0],
					'OptL' => $opt[1] ?? null,
					'Mod' => $opt[2] ?? ''
				]
			);
			$this->_optsS[$opt[0]] = $k;
			if (isset($opt[1])) {
				$this->_optsL[$opt[1]] = $k;
			}
		}
		return $this;
	}

	/**
	* Return Object(HuGetopt_option) by its string 'w', or 'what'
	*
	* @param string $str Option string
	* @param string $type Type: s|l|a (s - Short, l - Long, a - Auto detect by length)
	* @return HuGetoptOption
	**/
	public function &getOptByStr(string $str, string $type = 'a'): HuGetoptOption {
		$typeArray = null;
		switch ($type) {
			case 's':
				$typeArray =& $this->_optsS;
				break;

			case 'l':
				$typeArray =& $this->_optsL;
				break;

			default:
				if (1 === \strlen($str)) {
					$typeArray =& $this->_optsS;
				} else {
					$typeArray =& $this->_optsL;
				}
				break;
		}
		return $this->_opts[$typeArray[$str]];
	}

	/**
	* Main Horse!!! Doing most work.
	*
	* @throws VariableRequiredException
	**/
	public function parseArgs(): void {
		$this->_nonOpts->push($this->currentArg());
		while ($cArg = $this->nextArg()) {
			if ('--' === $cArg) {
				$this->_nonOpts->pushArray(\array_splice($this->argv, $this->_curArgv));
				break;
			}

			// Peek at next arg BEFORE getOpt (which may consume it)
			$this->_peekedForNext = $this->peekNextArg();

			if (null === ($o = $this->getOpt($cArg))) {
				$this->_nonOpts->push($cArg);
				continue;
			}

			// reference. All modification - in-place.
			/** @var HuGetoptOption $o */
			$o = $this->getOptByStr($o->Opt[0], $o->OptT[0])->add($o);

			/** @var string $mod */
			$mod = $o->Mod;
			if ('' === $mod) {
				/** @var HuArray $val */
				$val = $o->Val;
				$val->_last_ = true;
			} else {
				// : or ::
				// Check if Val already has explicit string value (e.g., from long option form --opt=value or short option -ovalue)
				// Note: add() pushes the option object, so we need to check if Val has a string value
				/** @var HuArray $val */
				$val = $o->Val;
				/** @var string|null $val0 */
				$val0 = $val[0] ?? null;
				$hasExplicitValue = \is_string($val0);
				/** @var bool $optarg */
				/** @var bool $lastVal2 */
				$lastVal2 = $val->_last_;
				$optarg = $lastVal2; // def
				if (!$hasExplicitValue) { // If NOT long option '=' form or short option `-ovalue` form
					// Use pre-peeked value from main loop (before isOpt consumed it)
					$peekedArg = $this->_peekedForNext;
					$isOptPeeked = false !== $peekedArg && \preg_match('/^-./', $peekedArg);
					if ($isOptPeeked) {
						// Next is option (starts with -)
						if (':' === $mod) {
							// For : - error (required)
							/** @var string|null $optS */
							$optS = $o->OptS;
							/** @var HuArray $opt */
							$opt = $o->Opt;
							/** @var string|null $optLast */
							$optLast = $opt->_last_;
							$optName = $optS ?? $optLast;
							throw new VariableRequiredException(new Backtrace(), $optName, 'Option [' . $optName . '] requires argument!');
						}
						// For :: - leave default (optional), don't consume
					} else {
						// Not an option (or no more args), consume it
						$nextArg = $this->nextArg();
						if (false !== $nextArg) {
							// Use as value
							$optarg = $nextArg;
						} else {
							// No more args → error for : (required)
							/** @var string $mod2 */
							$mod2 = $o->Mod;
							if (':' === $mod2) {
								/** @var string|null $optS2 */
								$optS2 = $o->OptS;
								/** @var HuArray $opt2 */
								$opt2 = $o->Opt;
								/** @var string|null $optLast2 */
								$optLast2 = $opt2->_last_;
								$optName = $optS2 ?? $optLast2;
								throw new VariableRequiredException(new Backtrace(), $optName, 'Option [' . $optName . '] requires argument!');
							}
							// For :: - no error, leave default
						}
					}
				}
				/** @var HuArray $valFinal */
				$valFinal = $o->Val;
				$valFinal->_last_ = $optarg;
			}
		}
	}

	/**
	* Move internal pointer to next arg, and return it.
	*
	* @return string|false
	**/
	protected function nextArg(): string|false {
		if ($this->_curArg !== null) {
			$tmp = $this->_curArg;
			$this->_curArg = null;
			return $tmp;
		} elseif (++$this->_curArgv < \sizeof($this->argv)) {
			return $this->argv[$this->_curArgv];
		} else {
			return false;
		}
	}

	/**
	* Peek at next arg without consuming it.
	*
	* @return string|false
	**/
	protected function peekNextArg(): string|false {
		$pos = $this->_curArgv + 1;
		if (null !== $this->_curArg) {
			$pos = $this->_curArgv;
		}
		if ($pos < \sizeof($this->argv)) {
			return $this->argv[$pos];
		}
		return false;
	}

	/**
	* Return current argument
	*
	* @return string
	**/
	protected function currentArg(): string {
		if ($this->_curArg !== null) {
			$tmp = $this->_curArg;
			$this->_curArg = null;
			return $tmp;
		} else {
			return $this->argv[$this->_curArgv];
		}
	}

	/**
	* Get option from argument or false if not an option.
	*
	* @param string $arg Usually element of $argv
	* @return ?HuGetoptOption
	**/
	protected function getOpt(string $arg): ?HuGetoptOption {
		$r = $this->getShortOpt($arg);
		return null !== $r ? $r : $this->getLongOpt($arg);
	}

	/**
	* Get short option from argument.
	*
	* @param string $arg Arg-string to check
	* @return ?HuGetoptOption In object ->Val NOT filled for options with modifiers. For exception see description {@see ::getLongOpt()}
	**/
	public function getShortOpt(string $arg): ?HuGetoptOption {
		$re = new RegExpPcre(
			'/^(' . \implode('|', RegExpPcre::quote($this->getSettings()->start_short)) . ')(' . \implode('|', \array_keys($this->_optsS)) . ')(.*)/s',
			$arg
		);
		$re->doMatch();

		if ($re->matchCount() > 0) {
			// Handle sequence of short options without opt-arguments. E.g. `-otfs`.
			$o = $this->getOptByStr($re->match(2)[0], 's');
			if (':' === $o->Mod || '::' === $o->Mod) {
				// Have optarg - but don't call nextArg() here, let parseArgs handle it
				// If there's a value in the same arg (e.g., -fvalue), use it
				$hasInlineValue = '' !== (string) $re->match(3)[0];
				return new HuGetoptOption(
					$this->getSettings()->HuGetopt_option_options,
					[
						'Sep' => new HuArray([$re->match(1)[0]]),
						'Opt' => new HuArray([$re->match(2)[0]]),
						'Val' => new HuArray([$hasInlineValue ? $re->match(3)[0] : null]),
						'OptT' => new HuArray(['s'])
					]
				);
			} else {
				// Not have optarg => $re->match(2) is continue of non-optarg options.
				if ('' !== $re->match(3)[0]) {
					$this->_curArg = '-' . $re->match(3)[0];
				}
				return new HuGetoptOption(
					$this->getSettings()->HuGetopt_option_options,
					[
						'Sep' => new HuArray([$re->match(1)[0]]),
						'Opt' => new HuArray([$re->match(2)[0]]),
						'Val' => new HuArray([null]),
						'OptT' => new HuArray(['s'])
					]
				);
			}
		}
		return null;
	}

	/**
	* Get long option from argument.
	* But, BE CAREFUL ->Val will be filled in only one case: See additional
	* 5 in main description of class HuGetopt about bug in php-cli to parse
	* --longOpt='optarg' and --longOpt'optarg' forms of long options. In
	* this form, when value of arg in same element of $argv - this it parsed
	* and filled ->Val with this value, and ->= set to true. In other cases,
	* next argument not got!
	*
	* @param string $arg Arg-string to check
	* @return ?HuGetoptOption
	**/
	public function getLongOpt(string $arg): ?HuGetoptOption {
		$startPatterns = $this->getSettings()->alternative
			? \array_merge($this->getSettings()->start_long, $this->getSettings()->start_short)
			: $this->getSettings()->start_long;
		$re = new RegExpPcre(
			'/^(' . \implode('|', RegExpPcre::quote($startPatterns)) . ')(' . \implode('|', \array_keys($this->_optsL)) . ')(=|(?>\s*))(.*)/s',
			$arg
		);
		$re->doMatch();

		if ($re->matchCount() > 0) {
			return new HuGetoptOption(
				$this->getSettings()->HuGetopt_option_options,
				[
					'Sep' => new HuArray([$re->match(1)[0]]),
					'Opt' => new HuArray([$re->match(2)[0]]),
					'=' => new HuArray([$re->match(3)[0]]),
					'Val' => new HuArray([$re->match(4)[0] !== '' ? $re->match(4)[0] : $this->nextArg()]),
					'OptT' => new HuArray(['l'])
				]
			);
		}
		return null;
	}

	/**
	* Set new array of arguments
	*
	* @param array<string> $argv Arguments array
	* @return $this
	**/
	public function &setArgv(array $argv): static {
		$this->argv = $argv;
		return $this;
	}

	/**
	* Short alias for {@see ::getOptByStr()}
	*
	* @param string $opt Option name
	* @param string $type Type: s|l|a (default 'a')
	* @return HuGetoptOption
	**/
	public function get(string $opt, string $type = 'a'): HuGetoptOption {
		return $this->getOptByStr($opt, $type);
	}

	/**
	* Object(HuArray) of NonOption arguments. all, 0 - by default is name of self script!
	*
	* @param int $from Start from element. Very useful value 1, to ignore script-name.
	* @return HuArray
	**/
	public function getNonOpts(int $from = 0): HuArray {
		return $this->_nonOpts->getSlice($from);
	}

	/**
	* Return array known (defined for parsing, not parsed!) short options.
	*
	* @return array<string, int>
	**/
	public function getListShortOpts(): array {
		return $this->_optsS;
	}

	/**
	* Return array known (defined for parsing, not parsed!) long options.
	*
	* @return array<string, int>
	**/
	public function getListLongOpts(): array {
		return $this->_optsL;
	}

	/**
	* Idea (and method name) got from PEAR Console_getopt and adopted, modified.
	* Safely read the $argv PHP array across different PHP configurations.
	* Will take care on register_globals and register_argc_argv ini directives
	*
	* @return $this
	* @throws VariableEmptyException
	**/
	public function &readPHPArgv(): static {
		global $argv;

		if (\is_array($argv)) {
			$this->setArgv($argv);
		} elseif (@\is_array($_SERVER['argv'])) {
			$this->setArgv($_SERVER['argv']);
		} elseif (@\is_array($GLOBALS['HTTP_SERVER_VARS']['argv'])) {
			$this->setArgv($GLOBALS['HTTP_SERVER_VARS']['argv']);
		} else {
			throw new VariableEmptyException(new Backtrace(), 'readPHPArgv(): Could not read cmd args (register_argc_argv=Off?)');
		}

		return $this;
	}
}
