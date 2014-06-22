<?
$GLOBALS['__CONFIG']['db'] = 'mysql_database'; //Current used DB configuration

$GLOBALS['__CONFIG']['mysql_database'] = array(
	'class_file'	=> 'Database/mysql_database.php'
	,'class_name'	=> 'mysql_database'

	,'hostname'	=> 'mysqlserver'
	,'username'	=> 'amur_u'
	,'password'	=> 'password'
	,'dbName'		=> 'amur'
	,'persistent'	=> true
	,'DEBUG'		=> true
	,'CHARSET_RECODE' => false
);
?>
