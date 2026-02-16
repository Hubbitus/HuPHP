<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Vars\Settings\Settings;

class HuErrorSettings extends Settings {
	// Defaults
	protected $__SETS = [
		/**
		* @example HuLOG.php
		**/
		'FORMAT_WEB'		=> [],	/** For strToWeb().		If empty (by default): dump::w */
		'FORMAT_CONSOLE'	=> [],	/** For strToConsole().	If empty (by default): dump::c */
		'FORMAT_FILE'		=> [],	/** For strToFile().	If empty (by default): dump::log */

		/**
		* @see ::updateDate()
		**/
		'AUTO_DATE'		=> true,
		'DATE_FORMAT'		=> 'Y-m-d H:i:s',
    ];

	/**
	* @example
	* protected $__SETS = array(
	*	//В формате settings::getString(array)
	*	'FORMAT_CONSOLE'	=> array(
	*		array('date', "\033[36m", "\033[0m"),
	*		'level',
	*		array('type', "\033[1m", "\033[0m: ", ''),//Bold
	*		'logText',
	*		array('extra', "\n"),
	*		"\n"
	*	),
	*	'FORMAT_WEB'	=> array(
	*		array('date', "<b>", "</b>"),
	*		'level',
	*		array('type', "<b>", "</b>: ", ''),
	*		'logText',
	*		array('extra', "<br\\>\n"),
	*		"<br\\>\n"
	*	),
	*	'FORMAT_FILE'	=> array(
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
