<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug\Format;

use Hubbitus\HuPHP\System\OutputType;

class PrintoutDefault {
	/**
	* Helper to more flexibility show large amount of data (long strings, dump of arrays etc.)
	*
	* @param string $shortVar
	* @param string $longVar
	* @param string $innerTagStart
	* @param string $innerTagEnd
	* @return string
	**/
	public static function backtracePrintoutWebHelper(string $shortVar, string $longVar, string $innerTagStart = '<textarea', string $innerTagEnd = '</textarea>'): string {
		$str = '<span title="' . $longVar . '" onclick=\'' .
			'this.onclickBak=this.onclick; this.onclick=null; var ttt = this.innerHTML; this.innerHTML="' . $innerTagStart . ' style=\\"color: green; width: 50em; height: 7em; overflow: auto\\" ondblclick=\\"this.parentNode.onclick=this.parentNode.onclickBak; var ttt=this.parentNode.title; this.parentNode.title=( this.defaultValue ? this.defaultValue : this.innerHTML); this.parentNode.innerHTML = ttt; \\" + this.title + "' . $innerTagEnd . '"; this.title = ttt;\'' .
			'>' . $shortVar . '</span>';
		return '\'' . $str . '\'';
	}

	public static function configure(): void {
		/** For format description see {@link HuFormat class} **/
		$GLOBALS['__CONFIG']['backtrace::printout'] = [
			OutputType::WEB->name => [
				'A:::' => [
					"<div style='text-align: left; font-family: monospace; padding-left:2em'>\n<b style='color: brown'>Backtrace:</b><br />",
					[
						'I:::call' => [
							'A:::' => [
								'<b style=\'color: green\'>call:</b> ',
								['sn:::class', '<span style=\'color: purple\'>', '</span>'],
								['sn:::type', '<span style=\'color: brown\'>', '</span>'],
								['sn:::function', '<span style=\'color: magenta\'>', '</span>'],
								['E:::args', '\'(\'.$var->formatArgs().\')\''],
								"<br />\n",

								'&rArr;&rArr;<b style=\'color: red\'>file:</b> ',
								['sn:::file', '<u>', '</u>'],
								' - ',
								['sn:::line', '<b style=\'background-color: orange\'>', '</b>'],
								"<br />\n"
							],
						],
					],
					"</div>\n",
				],

				'argtypes' => [
					'integer' => ['v:::'], // As is
					'double' => ['v:::'],
					'string' => ['E:::', self::backtracePrintoutWebHelper('\\\'\'.htmlspecialchars(substr($var, 0, 32)).((($sl = strlen($var)) < 32) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}', '\'.htmlspecialchars($var).\'')],
					'array' => ['E:::', self::backtracePrintoutWebHelper('\'.\'Array(\'.sizeof($var).\')\'.\'', '\'.htmlspecialchars(dump::byOutType(OutputType::WEB, $var, null, true)).\'', '<div style=\\\"display: table; border: thick dashed green; border-top: none\\\"', '</div>')],
					'object' => ['E:::', '\'Object(\'.get_class($var).\')\''],
					'resource' => ['E:::', '\'Resource(\'.strstr($var, \'#\').\')\''],
					'boolean' => ['E:::', '$var ? \'True\' : \'False\''],
					'NULL' => 'Null',
					'default' => ['n:::', 'Unknown (', ')'],
				],
			],

			OutputType::CONSOLE->name => [
				'A:::' => [
					"\033[1mBacktrace:\033[0m\n",
					[
						'I:::call' => [
							'A:::' => [
								"\033[32mcall:\033[0m ",
								['sn:::class', "\033[35m", "\033[0m"],
								['sn:::type', "\033[33m", "\033[0m"],
								['sn:::function', "\033[35;1m", "\033[0m"],
								['E:::args', '"\033[33m(\033[0m".$var->formatArgs()."\033[33m)\033[0m"'],
								"\n",

								"\t->\033[31;1mfile: \033[0m",
								['sp:::file', '%s', '__vAr__'],
								':',
								['sn:::line', "\033[43;1m", "\033[0m"],
								"\n",
							],
						],
					],
				],
			],
			OutputType::FILE->name => [
				'A:::' => [
					"Backtrace:\n",
					[
						'I:::call' => [
							'A:::' => [
								'call: ',
								['s:::class'],
								['s:::type'],
								['s:::function'],
								['E:::args', '\'(\'.$var->formatArgs().\')\''],
								"\n",

								'file: ',
								['s:::file'],
								':',
								['s:::line'],
								"\n",
							]
						]
					]
				]
			]
		];

		$GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name]['argtypes'] = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name]['argtypes'];
		$GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name]['argtypes'] =& $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name]['argtypes'];

		// Difference in argTypes
		$GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name]['argtypes']['string']
			= $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name]['argtypes']['string']
			= ['E:::', '\'\\\'\'.htmlspecialchars(substr($var, 0, 28)).((($sl = strlen($var)) < 28) ? \'\' : \'...\').\'\\\'{\'.$sl.\'}\''];

		$GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name]['argtypes']['array']
			= $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name]['argtypes']['array']
			= ['E:::', '\'Array(\'.count($var).\')\''];

		$GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name] =& $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name];
		$GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name] =& $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name];
		$GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name] =& $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name];
	}
}
