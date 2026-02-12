<?php
/**
* @uses HuLOG
**/

$GLOBALS['__CONFIG']['HuLOG'] = array(
	'FILE_PREFIX'		=> 'log_',
	'LOG_FILE_DIR'		=> './_log/',

	'LOG_TO_ACS' => HuLOGSettings::LOG_TO_BOTH,
	'LOG_TO_ERR' => HuLOGSettings::LOG_TO_BOTH,

	/* In SUBarray in order not to generate extra Entity  */
	'HuLOG_Text_settings' => array(
		'EXTRA_HEADER' => null, // NOT false!
	),
);
?>