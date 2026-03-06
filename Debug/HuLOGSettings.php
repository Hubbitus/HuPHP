<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\Vars\Settings\Settings;

class HuLOGSettings extends Settings {
	/** @var int Bitmask for print output */
	public const int LOG_TO_PRINT = 4;
	/** @var int Bitmask for file output */
	public const int LOG_TO_FILE = 8;
	/** @var int Bitmask for both file and print output */
	public const int LOG_TO_BOTH = 12; // FILE + PRINT

	protected array $__SETS = [
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
