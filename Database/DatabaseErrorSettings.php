<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Database;

use Hubbitus\HuPHP\Debug\HuErrorSettings;
use Hubbitus\HuPHP\System\OutputType;

/**
 * Database error settings class.
 *
 * @package Database
 **/
class DatabaseErrorSettings extends HuErrorSettings {
	/**
	 * Initialize default settings.
	 *
	 * @return void
	 **/
	protected function initDefaults(): void {
		parent::initDefaults();

		// Defaults
		$this->__SETS = [
			// Aka-Constants
			'TXT_queryFailed' => 'SQL Query failed',
			'TXT_cantConnect' => 'Could not connect to DB',
			'TXT_noDBselected' => 'Can not change database',

			/**
			 * @see HuError::updateDate()
			 */
			'AUTO_DATE' => true,
			'DATE_FORMAT' => 'Y-m-d H:i:s: ',

			/** Header for 'extra'-data, which may be present */
			'EXTRA_HEADER' => 'Extra info',
			// In format settings::getString(array)
			// To out in Browser
			OutputType::WEB->name => [
				['TXT_queryFailed', "\n<br \><u><b>", "</b></u>:\n<br \>", ''],
				'server_message',
				['errNo', ' (', ')'],
				['server_messageS', "<br \>\n<b>", '</b>'],
				"\n<br><u>On query:</u> ",
				['Query', '<pre style="color: red">', '</pre>'],
				['bt', "<br \>\n"]
			],
			// In CLI-mode
			OutputType::CONSOLE->name => [
				['TXT_queryFailed', "\033[1m", "\033[0m:\n", ''],
				'server_message',
				['errNo', '(', ')'],
				['server_messageS', "\n\033[31;1m", "\033[0m"],
				"\n\033[4;1mOn query:\033[0m ",
				['Query', "\033[31m", "\033[0m"],
				['bt', "\n"]
			],
			// Primarily for logs (files)
			OutputType::FILE->name => [
				['TXT_queryFailed', '', ":\n"],
				'server_message',
				['errNo', '(', ')'],
				['server_messageS', "\n", ""],
				"\nOn query:",
				['Query', "=>", "<="],
				['bt', "\n"]
			]
		];
	}
}
