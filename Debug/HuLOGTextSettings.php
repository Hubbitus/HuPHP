<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;
use Hubbitus\HuPHP\System\OutputType;

class HuLOGTextSettings extends HuErrorSettings {
	private array $initialSETS = [
		/**
		* @see HuError::updateDate()
		**/
		'AUTO_DATE'   => true,
		'DATE_FORMAT' => 'Y-m-d H:i:s:',

		/** Header for 'extra'-data, which may be present */
		'EXTRA_HEADER'		=> 'Extra info',

		/** In format {@link settings::getString()} */
		OutputType::CONSOLE->name => [
			['date', "\033[36m", "\033[0m"],
			'level',
			['type', "\033[1m", "\033[0m: ", ''], //Bold
			'logText',
			['extra', "\n"],
			"\n"
		],
		OutputType::WEB->name => [
			['date', "<b>", "</b>"],
			'level',
			['type', "<b>", "</b>: ", ''],
			'logText',
			['extra', "<br>\n"],
			"<br>\n"
		],
		OutputType::FILE->name => [
			'date',
			'level',
			['type', '', ': ', ''],
			'logText',
			['extra', "\n"],
			"\n"
		]
	];

	public function __construct(array $array = []) {
		parent::__construct($array);
		$this->mergeSettingsArray($this->initialSETS);
	}
}
