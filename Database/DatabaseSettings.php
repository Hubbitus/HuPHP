<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Database;

use Hubbitus\HuPHP\Vars\Settings\Settings;

/**
* Database settings class.
*
* @package Database
**/
class DatabaseSettings extends Settings {
	public function __construct() {
		parent::__construct();
		$this->initDefaults();
	}

	/**
	 * Initialize default settings.
	 *
	 * @return void
	 **/
	protected function initDefaults(): void {
		$this->__SETS = [
			/*
				'hostname'	=> 'localhost',
				'username'	=> 'root',
				'password'	=> '',
				'dbName'		=> 'grub',
				'persistent'	=> true,
				'charset'		=> 'CP1251' // TO fire SET NAMES... {@see ::set_names()}
			*/
			'CHARSET_RECODE' => [
				'FROM'	=> 'CP1251',	// Script charset
				'TO'	=> 'UTF-8'	// DB charset
			],
			'DEBUG'		=> false,

			/** In SUBarray in order not to generate extra Entity */
			'DBError_settings' => [
				// Here may be overwritten defaults settings. {@see HuLOG_text_settings}
			]
		];
	}
}
