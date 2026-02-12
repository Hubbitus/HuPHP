<?php
declare(strict_types=1);

class DatabaseSettings extends Settings {
	// Defaults
	protected $__SETS = [
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
}#c