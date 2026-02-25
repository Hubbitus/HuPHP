<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\System\OutputType;
use Hubbitus\HuPHP\Vars\Settings\Settings;

/**
* HuError settings class.
*
* @package Debug
*
* @property array<mixed> $WEB (OutputType) Format for web output
* @property array<mixed> $CONSOLE (OutputType::) Format for console output
* @property array<mixed> $FILE OutputType Format for file output
* @property bool $AUTO_DATE Auto-update date flag
* @property string $DATE_FORMAT Date format string
**/
class HuErrorSettings extends Settings {
	// Defaults
	public function __construct(){
		parent::__construct();
		$this->__SETS = [
			/**
			* @example HuLOG.php
			**/
			OutputType::WEB->name => 	   [], /** For strForWeb().		If empty (by default): dump::w */
			OutputType::CONSOLE->name => [], /** For strForConsole().	If empty (by default): dump::c */
			OutputType::FILE->name => [], /** For strForFile().	If empty (by default): dump::log */

			/**
			* @see ::updateDate()
			**/
			'AUTO_DATE'		=> true,
			'DATE_FORMAT'		=> 'Y-m-d H:i:s',
		];
	}

	/**
	* @example
	* protected $__SETS = array(
	*	//In format if Settings::getString(array)
	*	OutputType::CONSOLE->name => array(
	*		array('date', "\033[36m", "\033[0m"),
	*		'level',
	*		array('type', "\033[1m", "\033[0m: ", ''),//Bold
	*		'logText',
	*		array('extra', "\n"),
	*		"\n"
	*	),
	*	OutputType::WEB->name => array(
	*		array('date', "<b>", "</b>"),
	*		'level',
	*		array('type', "<b>", "</b>: ", ''),
	*		'logText',
	*		array('extra', "<br\\>\n"),
	*		"<br\\>\n"
	*	),
	*	OutputType::FILE->name => array(
	*		'date',
	*		'level',
	*		array('type', '', ': ', ''),
	*		'logText',
	*		array('extra', "\n"),
	*		"\n"
	*		),
	*	),
	* );
	**/
}
