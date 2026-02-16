<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

class HuLOGTextSettings extends HuErrorSettings{
	protected $__SETS = array(
		/**
		* @see HuError::updateDate()
		*/
		'AUTO_DATE'		=> true,
		'DATE_FORMAT'		=> 'Y-m-d H:i:s:',

		/** Header for 'extra'-data, which may be present */
		'EXTRA_HEADER'		=> 'Extra info',

		/** In format {@link settings::getString()} */
		'FORMAT_CONSOLE'	=> array(	//Формат вывода для отладки
			array('date', "\033[36m", "\033[0m"),
			'level',
			array('type', "\033[1m", "\033[0m: ", ''),//Bold
			'logText',
			array('extra', "\n"),
			"\n"
		),
		'FORMAT_WEB'		=> array(
			array('date', "<b>", "</b>"),
			'level',
			array('type', "<b>", "</b>: ", ''),
			'logText',
			array('extra', "<br>\n"),
			"<br>\n"
		),
		'FORMAT_FILE'		=> array(
			'date',
			'level',
			array('type', '', ': ', ''),
			'logText',
			array('extra', "\n"),
			"\n"
		)
	);
}
