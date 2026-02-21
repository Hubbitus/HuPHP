<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Vars\Settings\Settings;
use Hubbitus\HuPHP\System\OS;

class HuLOGSettings extends Settings{
	const LOG_TO_FILE	= OS::OUT_TYPE_FILE; // To file
	const LOG_TO_PRINT	= OS::OUT_TYPE_PRINT; // To stdout (print, echo)
	// Unfortunately PHP does NOT support computed value of constants
	//const LOG_TO_BOTH	= OS::OUT_TYPE_FILE + OS::OUT_TYPE_PRINT;	//to both
	const LOG_TO_BOTH	= 12; // to both

	protected $__SETS = [
		'FILE_PREFIX'		=> 'log_',
		'LOG_FILE_DIR'		=> './log/',

		'LOG_TO_ACS'		=> self::LOG_TO_BOTH,
		'LOG_TO_ERR'		=> self::LOG_TO_BOTH,

		/** In SUBarray in order not to generate extra Entity
		'HuLOG_Text_settings' => array(
			// Here may be overwritten defaults settings. {@see HuLOG_text_settings}
		)
		*/
	];
}
