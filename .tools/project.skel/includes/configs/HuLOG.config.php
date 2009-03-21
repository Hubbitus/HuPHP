<?
include_once('Debug/HuLOG.php');#Там константы определены

$GLOBALS['__CONFIG']['HuLOG'] = array(
#	'FILE_PREFIX'		=> 'log_',
	'LOG_FILE_DIR'		=> './_log/',

#	'LOG_TO_ACS' => HuLOG_settings::LOG_TO_BOTH,
#	'LOG_TO_ERR' => HuLOG_settings::LOG_TO_BOTH,
	'LOG_TO_ACS' => HuLOG_settings::LOG_TO_FILE,
	'LOG_TO_ERR' => HuLOG_settings::LOG_TO_FILE,

	/* In SUBarray in order not to generate extra Entity  */
	'HuLOG_Text_settings' => array(
		'EXTRA_HEADER' => null, #NOT false!
	),
);
?>